<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'role',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Credit the wallet balance.
     */
    public function credit($amount, $description, $performerId, $metadata = null, $referenceType = null, $referenceId = null)
    {
        return DB::transaction(function () use ($amount, $description, $performerId, $metadata, $referenceType, $referenceId) {
            $wallet = static::query()->whereKey($this->id)->lockForUpdate()->firstOrFail();
            $wallet->balance += $amount;
            $wallet->save();

            $this->setRawAttributes($wallet->getAttributes(), true);

            return $wallet->transactions()->create([
                'user_id' => $wallet->user_id,
                'amount' => $amount,
                'balance_after' => $wallet->balance,
                'type' => 'credit',
                'description' => $description,
                'performed_by' => $performerId,
                'metadata' => $metadata,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);
        });
    }

    /**
     * Debit the wallet balance.
     */
    public function debit($amount, $description, $performerId, $metadata = null, $referenceType = null, $referenceId = null)
    {
        return DB::transaction(function () use ($amount, $description, $performerId, $metadata, $referenceType, $referenceId) {
            $wallet = static::query()->whereKey($this->id)->lockForUpdate()->firstOrFail();

            if ($wallet->balance < $amount) {
                throw new \Exception('Insufficient wallet balance.');
            }

            $wallet->balance -= $amount;
            $wallet->save();

            $this->setRawAttributes($wallet->getAttributes(), true);

            return $wallet->transactions()->create([
                'user_id' => $wallet->user_id,
                'amount' => $amount,
                'balance_after' => $wallet->balance,
                'type' => 'debit',
                'description' => $description,
                'performed_by' => $performerId,
                'metadata' => $metadata,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);
        });
    }
}
