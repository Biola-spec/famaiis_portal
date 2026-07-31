<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class LearnhubSubject extends Model
{
    protected $fillable = ['name', 'description', 'teacher_id', 'class_id', 'year_id', 'term_id', 'total_weeks'];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function studentClass(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function year(): BelongsTo
    {
        return $this->belongsTo(StudentYear::class, 'year_id');
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'term_id');
    }

    public function weeks(): HasMany
    {
        return $this->hasMany(LearnhubWeek::class, 'subject_id')->orderBy('week_number');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(LearnhubLesson::class, LearnhubWeek::class, 'subject_id', 'week_id');
    }

    public function liveSessions(): HasMany
    {
        return $this->hasMany(LearnhubLiveSession::class, 'subject_id')->orderByDesc('created_at');
    }
}
