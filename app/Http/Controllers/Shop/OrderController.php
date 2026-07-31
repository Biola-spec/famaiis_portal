<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Throwable;

class OrderController extends Controller
{
    public function __construct(private readonly PaymentGatewayManager $gatewayManager)
    {
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Order::with('user');

        if (!$user->hasRole('Admin', 'Accountant')) {
            $query->where('user_id', $user->id);
        } else {
            // Admin/Accountant Filters
            if ($request->role) {
                $query->where('role_type', $request->role);
            }
            if ($request->status) {
                $query->where('status', $request->status);
            }
            if ($request->date) {
                $query->whereDate('created_at', $request->date);
            }
        }

        $orders = $query->latest()->paginate(15);
        return view('shop.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items.product', 'user', 'verifier');
        return view('shop.orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $cartItems = Cart::where('user_id', $user->id)->with('product')->get();
        $paymentSetting = PaymentSetting::first();

        $validated = $request->validate([
            'transfer_reference' => 'required|string|max:255',
            'transfer_receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with([
                'message' => 'Your cart is empty.',
                'alert-type' => 'error',
            ]);
        }

        foreach ($cartItems as $item) {
            if ($item->product->stock_quantity < $item->quantity) {
                return redirect()->back()->with([
                    'message' => "Insufficient stock for {$item->product->name}.",
                    'alert-type' => 'error',
                ]);
            }
        }

        if (!$paymentSetting || !$paymentSetting->bank_transfer_enabled || !$paymentSetting->bank_name || !$paymentSetting->account_number || !$paymentSetting->account_name) {
            return redirect()->back()->with([
                'message' => 'Bank transfer checkout is not configured yet. Please contact the school office.',
                'alert-type' => 'error',
            ]);
        }

        $receiptPath = $request->file('transfer_receipt')->store('shop/receipts', 'public');

        $order = DB::transaction(function () use ($user, $cartItems, $validated, $receiptPath) {
            $reference = 'SHOP-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(8));

            $order = Order::create([
                'user_id' => $user->id,
                'role_type' => $this->getUserRoleType($user),
                'total_amount' => $cartItems->sum(fn($item) => $item->product->price * $item->quantity),
                'status' => 'pending',
                'payment_reference' => $reference,
                'payment_provider' => 'bank_transfer',
                'payment_method' => 'bank_transfer',
                'payment_status' => 'submitted',
                'transfer_reference' => $validated['transfer_reference'],
                'transfer_receipt' => $receiptPath,
                'receipt_submitted_at' => Carbon::now(),
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            Cart::where('user_id', $user->id)->delete();

            // Notify Admins & Accountants
            $staff = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['Admin', 'Accountant']);
            })->get();
            
            Notification::send($staff, new NewOrderNotification($order));

            return $order;
        });

        return redirect()->route('orders.show', $order)->with([
            'message' => 'Transfer receipt submitted. Your order is waiting for admin verification.',
            'alert-type' => 'success',
        ]);
    }

    public function paymentCallback(Request $request)
    {
        $reference = (string) $request->query('reference');
        if ($reference === '') {
            return redirect()->route('orders.index')->with([
                'message' => 'Missing payment reference.',
                'alert-type' => 'error',
            ]);
        }

        $order = Order::where('payment_reference', $reference)->first();
        if (!$order) {
            return redirect()->route('orders.index')->with([
                'message' => 'Order not found for this payment.',
                'alert-type' => 'error',
            ]);
        }

        try {
            $verification = $this->gatewayManager->driver($order->payment_provider ?: 'paystack')->verify($reference);
        } catch (Throwable $e) {
            $order->update(['payment_status' => 'failed']);
            Log::error('Shop payment verification failed', [
                'order_id' => $order->id,
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('orders.index')->with([
                'message' => 'Payment verification failed: ' . $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        $isValid = ($verification['status'] ?? null) === 'success'
            && (string) ($verification['reference'] ?? '') === $order->payment_reference
            && (int) ($verification['amount'] ?? 0) === (int) round($order->total_amount * 100);

        if (!$isValid) {
            $order->update(['payment_status' => 'failed']);
            return redirect()->route('orders.index')->with([
                'message' => 'Payment verification did not pass security checks.',
                'alert-type' => 'error',
            ]);
        }

        $order->update([
            'payment_status' => 'success',
            'payment_transaction_id' => (string) ($verification['id'] ?? $verification['transaction_id'] ?? $order->payment_reference),
            'payment_method' => (string) ($verification['channel'] ?? $verification['authorization']['channel'] ?? 'online'),
            'paid_at' => Carbon::now(),
        ]);

        return redirect()->route('orders.index')->with([
            'message' => 'Order payment verified successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,completed',
            'verification_note' => 'nullable|string|max:1000',
        ]);

        if ($validated['status'] === 'approved' && $order->status === 'pending') {
            if ($order->payment_provider === 'bank_transfer' && $order->payment_status !== 'submitted') {
                return redirect()->back()->with('error', 'Cannot approve this order until a transfer receipt is submitted.');
            }

            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product->stock_quantity < $item->quantity) {
                    return redirect()->back()->with('error', "Cannot approve. Insufficient stock for {$product->name}.");
                }
                $product->decrement('stock_quantity', $item->quantity);
            }

            $order->payment_status = 'success';
            $order->paid_at = Carbon::now();
            $order->verified_by = auth()->id();
            $order->verified_at = Carbon::now();
        }

        if ($validated['status'] === 'rejected') {
            $order->payment_status = 'failed';
            $order->verified_by = auth()->id();
            $order->verified_at = Carbon::now();
        }

        $order->status = $validated['status'];
        $order->verification_note = $validated['verification_note'] ?? $order->verification_note;
        $order->save();

        // Notify User
        $order->user->notify(new OrderStatusNotification($order));

        return redirect()->back()->with('success', "Order status updated to {$validated['status']}.");
    }

    public function invoice(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items.product', 'user');
        
        $pdf = Pdf::loadView('shop.orders.invoice', compact('order'));
        return $pdf->download("invoice-{$order->id}.pdf");
    }

    private function getUserRoleType($user)
    {
        if ($user->hasRole('Parent')) return 'parent';
        if ($user->hasRole('Teacher')) return 'teacher';
        if ($user->hasRole('Student')) return 'student';
        return 'other';
    }
}
