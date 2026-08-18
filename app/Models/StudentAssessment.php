<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAssessment extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'section_id',
        'year_id',
        'term',
        'teacher_comment',
        'head_teacher_comment',
        'cognitive_areas',
        'ai_comment_draft',
        'ai_status',
        'ai_flag',
    ];

    protected $casts = [
        'cognitive_areas' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function student_class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }
}
