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
    public function credit($amount, $description, $performerId, $metadata = null)
    {
        return DB::transaction(function () use ($amount, $description, $performerId, $metadata) {
            $this->balance += $amount;
            $this->save();

            return $this->transactions()->create([
                'user_id' => $this->user_id,
                'amount' => $amount,
                'type' => 'credit',
                'description' => $description,
                'performed_by' => $performerId,
                'metadata' => $metadata,
            ]);
        });
    }

    /**
     * Debit the wallet balance.
     */
    public function debit($amount, $description, $performerId, $metadata = null)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient wallet balance.');
        }

        return DB::transaction(function () use ($amount, $description, $performerId, $metadata) {
            $this->balance -= $amount;
            $this->save();

            return $this->transactions()->create([
                'user_id' => $this->user_id,
                'amount' => $amount,
                'type' => 'debit',
                'description' => $description,
                'performed_by' => $performerId,
                'metadata' => $metadata,
            ]);
        });
    }
}
