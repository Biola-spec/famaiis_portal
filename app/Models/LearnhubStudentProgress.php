<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearnhubStudentProgress extends Model
{
    public $timestamps = false;

    protected $fillable = ['student_id', 'lesson_id', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
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
