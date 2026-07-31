<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'term',
        'session',
        'title',
        'amount',
    ];

    public function studentClass(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
