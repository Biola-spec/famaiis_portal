<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    protected $guarded = [];

    // Valid categories
    const CATEGORIES = ['mandatory', 'optional', 'one-time', 'recurring'];

    // Items using this fee type across all structures
    public function feeItems()
    {
        return $this->hasMany(FeeItem::class, 'fee_type_id');
    }
}
