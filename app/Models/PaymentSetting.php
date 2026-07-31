<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'public_key',
        'secret_key',
        'payment_url',
        'bank_name',
        'account_name',
        'account_number',
        'transfer_instructions',
        'bank_transfer_enabled',
        'is_active',
    ];

    protected $casts = [
        'secret_key' => 'encrypted',
        'bank_transfer_enabled' => 'boolean',
        'is_active' => 'boolean',
    ];
}
