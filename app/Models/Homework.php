<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    protected $table = 'homeworks';
    protected $guarded = [];

    public function student_class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }

    public function school_subject()
    {
        return $this->belongsTo(SchoolSubject::class, 'subject_id', 'id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'homework_id', 'id');
    }
}
