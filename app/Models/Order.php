<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id',
        'role_type',
        'total_amount',
        'status',
        'payment_reference',
        'payment_transaction_id',
        'payment_method',
        'payment_provider',
        'payment_status',
        'paid_at',
        'transfer_reference',
        'transfer_receipt',
        'receipt_submitted_at',
        'verified_by',
        'verified_at',
        'verification_note',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'receipt_submitted_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
