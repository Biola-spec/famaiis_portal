<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LearnhubWeek extends Model
{
    protected $fillable = ['subject_id', 'week_number', 'title'];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(LearnhubSubject::class, 'subject_id');
    }

    public function lesson(): HasOne
    {
        return $this->hasOne(LearnhubLesson::class, 'week_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(LearnhubLesson::class, 'week_id');
    }
}
