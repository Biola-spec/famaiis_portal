<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    protected $guarded = [];

    // The student
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // The section for this fee record
    public function section()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    // The fee structure applied
    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class, 'fee_structure_id');
    }

    // Payments made against this fee
    public function payments()
    {
        return $this->hasMany(FeePayment::class, 'fee_structure_id', 'fee_structure_id')
            ->where('student_id', $this->student_id)
            ->where('section_id', $this->section_id);
    }

    // Recalculate balance from total_due and payments
    public function recalculateBalance()
    {
        $paid = FeePayment::where('student_id', $this->student_id)
            ->where('section_id', $this->section_id)
            ->where('fee_structure_id', $this->fee_structure_id)
            ->sum('amount_paid');

        $this->total_paid = $paid;
        $this->balance    = $this->total_due - $paid;
        $this->save();
    }
}
