<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearnhubLesson extends Model
{
    protected $fillable = ['week_id', 'title', 'content'];

    public function week(): BelongsTo
    {
        return $this->belongsTo(LearnhubWeek::class, 'week_id');
    }

    public function cbtQuestions(): HasMany
    {
        return $this->hasMany(LearnhubCbtQuestion::class, 'lesson_id')->orderBy('question_number');
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(LearnhubStudentProgress::class, 'lesson_id');
    }

    public function cbtAttempts(): HasMany
    {
        return $this->hasMany(LearnhubCbtAttempt::class, 'lesson_id');
    }

    public function liveSessions(): HasMany
    {
        return $this->hasMany(LearnhubLiveSession::class, 'lesson_id');
    }
}
