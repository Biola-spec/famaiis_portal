<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizRetake extends Model
{
    protected $guarded = [];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
