<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearnhubCbtQuestion extends Model
{
    protected $fillable = [
        'lesson_id', 'question_number', 'question',
        'option_a', 'option_b', 'option_c', 'option_d',
        'correct_answer', 'explanation',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(LearnhubLesson::class, 'lesson_id');
    }
}
