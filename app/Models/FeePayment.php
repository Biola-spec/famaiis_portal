<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payment_date' => 'date',
    ];

    // The student who paid
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // The section the payment relates to
    public function section()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    // The fee structure paid against
    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class, 'fee_structure_id');
    }

    // Auto-generate a receipt number
    public static function generateReceiptNo(): string
    {
        $last = static::latest()->first();
        $seq  = $last ? ((int) substr($last->receipt_no, 4)) + 1 : 1;
        return 'RCP-' . str_pad($seq, 6, '0', STR_PAD_LEFT);
    }
}
