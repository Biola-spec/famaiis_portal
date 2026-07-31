<?php

namespace App\Http\Controllers\Backend\Wallet;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminWalletController extends Controller
{
    public function ManageAll()
    {
        $wallets = Wallet::with('user')->paginate(30);
        return view('backend.wallet.admin.manage', compact('wallets'));
    }

    public function WalletCredit(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string'
        ]);

        $user = User::findOrFail($request->user_id);
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'role' => strtolower($user->role), 'status' => 'active']
        );

        $wallet->credit($request->amount, $request->description, Auth::id());

        return redirect()->back()->with([
            'message' => 'Wallet credited successfully.',
            'alert-type' => 'success'
        ]);
    }

    public function WalletDebit(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string'
        ]);

        $user = User::findOrFail($request->user_id);
        $wallet = $user->wallet;

        if (!$wallet || $wallet->balance < $request->amount) {
            return redirect()->back()->with([
                'message' => 'Insufficient balance or wallet not found.',
                'alert-type' => 'error'
            ]);
        }

        $wallet->debit($request->amount, $request->description, Auth::id());

        return redirect()->back()->with([
            'message' => 'Wallet debited successfully.',
            'alert-type' => 'success'
        ]);
    }
}
