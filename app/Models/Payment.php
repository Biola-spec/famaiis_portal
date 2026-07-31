<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fee_id',
        'amount',
        'reference',
        'transaction_id',
        'status',
        'payment_method',
        'provider',
        'paid_by_user_id',
        'paid_at',
        'provider_payload',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'provider_payload' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by_user_id');
    }

    public function fee(): BelongsTo
    {
        return $this->belongsTo(Fee::class);
    }
}
