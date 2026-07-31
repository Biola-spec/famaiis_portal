<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignClassTeacher extends Model
{
    protected $fillable = [
        'class_id',
        'section_id',
        'teacher_id',
        'student_year_id',
    ];

    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function studentYear()
    {
        return $this->belongsTo(StudentYear::class, 'student_year_id');
    }

    public function section()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }
}
