<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeItem extends Model
{
    protected $guarded = [];

    // The fee structure this item belongs to
    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class, 'fee_structure_id');
    }

    // The type of this fee (Tuition, Exam, etc.)
    public function feeType()
    {
        return $this->belongsTo(FeeType::class, 'fee_type_id');
    }
}
