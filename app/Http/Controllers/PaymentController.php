<?php

namespace App\Http\Controllers;

use App\Models\AssignStudent;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\StudentClass;
use App\Models\User;
use App\Services\Payments\PaymentGatewayManager;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PDF;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentGatewayManager $gatewayManager)
    {
    }

    public function studentDashboard(Request $request): View
    {
        $authUser = Auth::user();
        $student = $this->resolveStudentContext($authUser, $request->integer('student_id'));
        $selectedClassId = $this->resolveStudentClassId($student->id);
        $fees = Fee::query()
            ->with('studentClass')
            ->when($selectedClassId, fn ($q) => $q->where('class_id', $selectedClassId))
            ->orderByDesc('session')
            ->orderBy('term')
            ->get();

        $payments = Payment::query()
            ->with('fee.studentClass')
            ->where('user_id', $student->id)
            ->latest()
            ->get();

        $totalFees = (float) $fees->sum('amount');
        $paidAmount = (float) $payments->where('status', 'success')->sum('amount');
        $balance = max(0, $totalFees - $paidAmount);

        return view('backend.payment.student_dashboard', [
            'student' => $student,
            'children' => $authUser->hasRole('Parent') ? $authUser->children()->orderBy('name')->get() : collect(),
            'fees' => $fees,
            'payments' => $payments,
            'selectedStudentId' => $student->id,
            'totalFees' => $totalFees,
            'paidAmount' => $paidAmount,
            'balance' => $balance,
        ]);
    }

    public function initializePayment(Request $request, Fee $fee): RedirectResponse
    {
        $authUser = Auth::user();
        $student = $this->resolveStudentContext($authUser, $request->integer('student_id'));

        if ((int) $fee->class_id !== (int) $this->resolveStudentClassId($student->id)) {
            return back()->with([
                'message' => 'Selected fee is not available for this student class.',
                'alert-type' => 'error',
            ]);
        }

        $validated = $request->validate([
            'amount' => ['nullable', 'numeric', 'min:100', 'max:' . $fee->amount],
            'provider' => ['nullable', 'string', 'in:paystack,flutterwave'],
        ]);

        $provider = $validated['provider'] ?? 'paystack';
        $amount = isset($validated['amount']) ? round((float) $validated['amount'], 2) : (float) $fee->amount;
        $reference = $this->generateReference($provider);

        $payment = Payment::query()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'amount' => $amount,
            'reference' => $reference,
            'status' => 'pending',
            'provider' => $provider,
            'paid_by_user_id' => $authUser->id,
        ]);

        $callbackUrl = route('payment.callback');
        $email = $authUser->email ?: $student->email;

        try {
            $data = $this->gatewayManager->driver($provider)->initialize(
                $email,
                (int) round($amount * 100),
                $reference,
                $callbackUrl,
                [
                    'payment_id' => $payment->id,
                    'student_id' => $student->id,
                    'fee_id' => $fee->id,
                ]
            );
        } catch (Throwable $e) {
            $payment->update(['status' => 'failed']);

            return back()->with([
                'message' => 'Payment initialization failed: ' . $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        return redirect()->away((string) ($data['authorization_url'] ?? route('payment.student.dashboard')));
    }

    public function verifyPayment(Payment $payment): bool
    {
        if ($payment->status === 'success') {
            return true;
        }

        try {
            $verification = $this->gatewayManager->driver($payment->provider)->verify($payment->reference);
        } catch (Throwable) {
            $payment->update(['status' => 'failed']);
            return false;
        }

        $verifiedStatus = ($verification['status'] ?? null) === 'success';
        $verifiedReference = (string) ($verification['reference'] ?? '') === $payment->reference;
        $verifiedAmount = (int) ($verification['amount'] ?? 0) === (int) round($payment->amount * 100);

        if (!$verifiedStatus || !$verifiedReference || !$verifiedAmount) {
            $payment->update([
                'status' => 'failed',
                'provider_payload' => $verification,
            ]);
            return false;
        }

        $payment->update([
            'status' => 'success',
            'transaction_id' => (string) ($verification['id'] ?? $verification['transaction_id'] ?? $payment->reference),
            'payment_method' => (string) ($verification['channel'] ?? $verification['authorization']['channel'] ?? 'online'),
            'paid_at' => Carbon::now(),
            'provider_payload' => $verification,
        ]);

        return true;
    }

    public function paymentCallback(Request $request): RedirectResponse
    {
        $reference = (string) $request->query('reference');

        if ($reference === '') {
            return redirect()->route('payment.student.dashboard')->with([
                'message' => 'Missing transaction reference.',
                'alert-type' => 'error',
            ]);
        }

        $payment = Payment::query()->where('reference', $reference)->first();

        if (!$payment) {
            return redirect()->route('payment.student.dashboard')->with([
                'message' => 'Payment record not found.',
                'alert-type' => 'error',
            ]);
        }

        $verified = DB::transaction(function () use ($payment) {
            $lockedPayment = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();
            return $this->verifyPayment($lockedPayment);
        });

        return redirect()->route('payment.student.dashboard', ['student_id' => $payment->user_id])->with([
            'message' => $verified ? 'Payment verified and saved successfully.' : 'Payment could not be verified.',
            'alert-type' => $verified ? 'success' : 'error',
        ]);
    }

    public function receipt(Payment $payment)
    {
        $this->authorizePaymentAccess($payment);

        if ($payment->status !== 'success') {
            return back()->with([
                'message' => 'Receipt is only available for successful payments.',
                'alert-type' => 'error',
            ]);
        }

        $pdf = PDF::loadView('backend.payment.receipt', ['payment' => $payment->load('fee.studentClass', 'student', 'payer')]);
        return $pdf->download('receipt-' . $payment->reference . '.pdf');
    }

    public function adminPayments(Request $request): View
    {
        $filters = $request->validate([
            'class_id' => ['nullable', 'integer', 'exists:student_classes,id'],
            'term' => ['nullable', 'string'],
            'status' => ['nullable', 'in:pending,success,failed'],
        ]);

        $payments = Payment::query()
            ->with(['student', 'payer', 'fee.studentClass'])
            ->when(!empty($filters['class_id']), fn ($q) => $q->whereHas('fee', fn ($f) => $f->where('class_id', $filters['class_id'])))
            ->when(!empty($filters['term']), fn ($q) => $q->whereHas('fee', fn ($f) => $f->where('term', $filters['term'])))
            ->when(!empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('backend.payment.admin_payments', [
            'payments' => $payments,
            'classes' => StudentClass::query()->orderBy('name')->get(),
            'filters' => $filters,
            'terms' => Fee::query()->select('term')->distinct()->orderBy('term')->pluck('term'),
        ]);
    }

    public function exportReport(Request $request)
    {
        $filters = $request->validate([
            'class_id' => ['nullable', 'integer', 'exists:student_classes,id'],
            'term' => ['nullable', 'string'],
            'status' => ['nullable', 'in:pending,success,failed'],
        ]);

        $rows = Payment::query()
            ->with(['student', 'payer', 'fee.studentClass'])
            ->when(!empty($filters['class_id']), fn ($q) => $q->whereHas('fee', fn ($f) => $f->where('class_id', $filters['class_id'])))
            ->when(!empty($filters['term']), fn ($q) => $q->whereHas('fee', fn ($f) => $f->where('term', $filters['term'])))
            ->when(!empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->latest()
            ->get();

        $filename = 'payments_report_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Reference', 'Student', 'Paid By', 'Class', 'Term', 'Session', 'Title', 'Amount', 'Status', 'Method', 'Provider', 'Paid At']);

            foreach ($rows as $row) {
                fputcsv($output, [
                    $row->reference,
                    optional($row->student)->name,
                    optional($row->payer)->name,
                    optional($row->fee->studentClass)->name,
                    optional($row->fee)->term,
                    optional($row->fee)->session,
                    optional($row->fee)->title,
                    $row->amount,
                    $row->status,
                    $row->payment_method,
                    $row->provider,
                    optional($row->paid_at)->toDateTimeString(),
                ]);
            }

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function resolveStudentClassId(int $studentId): ?int
    {
        return AssignStudent::query()
            ->where('student_id', $studentId)
            ->latest('id')
            ->value('class_id');
    }

    private function resolveStudentContext(User $authUser, ?int $studentId = null): User
    {
        if ($authUser->hasRole('Student')) {
            return $authUser;
        }

        if ($authUser->hasRole('Parent')) {
            $children = $authUser->children()->pluck('users.id')->all();
            $selectedId = $studentId ?: ($children[0] ?? null);

            abort_if(!$selectedId || !in_array($selectedId, $children, true), 403, 'Unauthorized student selection.');
            return User::query()->findOrFail($selectedId);
        }

        abort(403, 'This action is only available to students or parents.');
    }

    private function authorizePaymentAccess(Payment $payment): void
    {
        $authUser = Auth::user();

        if ($authUser->hasRole('Admin') || $authUser->hasRole('Accountant')) {
            return;
        }

        if ($authUser->hasRole('Student') && (int) $payment->user_id === (int) $authUser->id) {
            return;
        }

        if ($authUser->hasRole('Parent')) {
            $childrenIds = $authUser->children()->pluck('users.id')->all();
            if (in_array((int) $payment->user_id, $childrenIds, true)) {
                return;
            }
        }

        abort(403);
    }

    private function generateReference(string $provider): string
    {
        do {
            $reference = strtoupper($provider) . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(8));
        } while (Payment::query()->where('reference', $reference)->exists());

        return $reference;
    }
}
