<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $guarded = [];

    // The section this structure belongs to
    public function section()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    // The class this structure applies to (nullable = applies to whole section)
    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }


    // The academic year/session
    public function year()
    {
        return $this->belongsTo(StudentYear::class, 'year_id');
    }

    // Individual line items in this structure
    public function feeItems()
    {
        return $this->hasMany(FeeItem::class, 'fee_structure_id');
    }

    // Student fee records based on this structure
    public function studentFees()
    {
        return $this->hasMany(StudentFee::class, 'fee_structure_id');
    }

    // Recalculate total from items
    public function recalculateTotal()
    {
        $total = $this->feeItems()->sum('amount');
        $this->update(['total_amount' => $total]);
        return $total;
    }
}
