<?php

namespace App\Http\Controllers\Backend\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function WalletView()
    {
        $user = Auth::user();
        
        // Ensure wallet exists
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0.00,
                'role' => strtolower($user->role ?: 'user'),
                'status' => 'active'
            ]
        );

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->with(['performer'])
            ->latest()
            ->paginate(10);

        // If parent, get children's virtual activity (payments made for them)
        $childrenTransactions = collect();
        if ($user->hasRole('Parent')) {
            $childrenIds = $user->children()->pluck('users.id')->all();
            $childrenTransactions = WalletTransaction::whereIn('user_id', $childrenIds)
                ->with(['user', 'performer'])
                ->latest()
                ->paginate(10, ['*'], 'children_page');
        }

        return view('backend.wallet.view_wallet', compact('wallet', 'transactions', 'childrenTransactions'));
    }

    public function WalletHistory()
    {
        $user = Auth::user();
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->with(['performer'])
            ->latest()
            ->paginate(20);

        return view('backend.wallet.history', compact('transactions'));
    }

    public function PayFeesView()
    {
        $user = Auth::user();
        if (!$user->hasRole('Parent', 'Admin')) {
            return redirect()->back()->with([
                'message' => 'You are not allowed to perform this action',
                'alert-type' => 'error'
            ]);
        }

        $children = collect();
        if ($user->hasRole('Parent')) {
            $children = $user->children()->get();
        } else if ($user->hasRole('Admin')) {
            $children = User::whereHas('roles', function($q){
                $q->where('name', 'Student');
            })->get();
        }

        $wallet = $user->wallet;

        return view('backend.wallet.pay_fees', compact('children', 'wallet'));
    }

    public function PayFeesStore(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'fee_id' => 'required|exists:fees,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $wallet = $user->wallet;
        if (!$wallet || $wallet->balance < $request->amount) {
            return redirect()->back()->with([
                'message' => 'Insufficient wallet balance.',
                'alert-type' => 'error'
            ]);
        }

        $student = User::findOrFail($request->student_id);
        $fee = Fee::findOrFail($request->fee_id);

        try {
            DB::transaction(function () use ($wallet, $student, $fee, $request, $user) {
                // 1. Debit Parent Wallet
                $wallet->debit(
                    $request->amount,
                    "School Fees Payment for " . $student->name . " (" . $fee->title . ")",
                    $user->id,
                    ['fee_id' => $fee->id, 'student_id' => $student->id]
                );

                // 2. Create activity record for Student (Virtual Wallet)
                $studentWallet = Wallet::firstOrCreate(
                    ['user_id' => $student->id],
                    ['balance' => 0.00, 'role' => 'student', 'status' => 'active']
                );
                
                // Student "receives" the payment benefit (Activity ledger)
                WalletTransaction::create([
                    'wallet_id' => $studentWallet->id,
                    'user_id' => $student->id,
                    'amount' => $request->amount,
                    'type' => 'debit', // It's a "debit" in the sense of spending on their behalf? 
                    // Actually, let's follow the user example:
                    // Student Example: -₦20,000 Fees Paid (by Parent)
                    'description' => "Fees Paid (by " . $user->name . ")",
                    'performed_by' => $user->id,
                    'metadata' => ['fee_id' => $fee->id, 'paid_by' => $user->id]
                ]);

                // 3. Record in main Payments table for accounting compatibility
                Payment::create([
                    'user_id' => $student->id,
                    'fee_id' => $fee->id,
                    'amount' => $request->amount,
                    'reference' => 'WALLET-' . strtoupper(uniqid()),
                    'status' => 'success',
                    'payment_method' => 'wallet',
                    'paid_by_user_id' => $user->id,
                    'paid_at' => now(),
                ]);
            });

            return redirect()->route('wallet.view')->with([
                'message' => 'Fees paid successfully using wallet.',
                'alert-type' => 'success'
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with([
                'message' => 'Error: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }

    public function FundWalletStore(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('Parent', 'Admin')) {
             return redirect()->back()->with([
                'message' => 'Unauthorized action.',
                'alert-type' => 'error'
            ]);
        }

        $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        // In a real app, this would redirect to a payment gateway.
        // For this demo/task, we'll implement a "Simulate Funding" or redirect to PaymentController if applicable.
        // Let's assume manual funding for now or a simulation if it's a Parent.
        
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0, 'role' => strtolower($user->role)]);
        
        $wallet->credit($request->amount, "Wallet Funding", $user->id);

        return redirect()->back()->with([
            'message' => 'Wallet funded successfully.',
            'alert-type' => 'success'
        ]);
    }

    public function GetStudentFees($student_id)
    {
        $student = User::findOrFail($student_id);
        
        // Resolve student class
        $class_id = DB::table('assign_students')
            ->where('student_id', $student_id)
            ->latest('id')
            ->value('class_id');

        if (!$class_id) {
            return response()->json([]);
        }

        $fees = Fee::where('class_id', $class_id)->get();
        
        // For each fee, check if it's already paid
        $feesWithStatus = $fees->map(function($fee) use ($student_id) {
            $paidAmount = Payment::where('user_id', $student_id)
                ->where('fee_id', $fee->id)
                ->where('status', 'success')
                ->sum('amount');
            
            $fee->paid_amount = $paidAmount;
            $fee->balance = $fee->amount - $paidAmount;
            return $fee;
        });

        return response()->json($feesWithStatus);
    }
}
