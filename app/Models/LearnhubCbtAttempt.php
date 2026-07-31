<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearnhubCbtAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'student_id', 'lesson_id', 'answers',
        'score', 'total_questions', 'passed', 'attempted_at',
        'game_points', 'max_streak', 'time_seconds',
    ];

    protected $casts = [
        'answers' => 'array',
        'passed' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(LearnhubLesson::class, 'lesson_id');
    }
}
