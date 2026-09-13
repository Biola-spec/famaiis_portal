<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    protected $table = 'homeworks';
    protected $guarded = [];

    protected $casts = [
        'approved_at' => 'datetime',
        'recalled_at' => 'datetime',
    ];

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

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function recalledBy()
    {
        return $this->belongsTo(User::class, 'recalled_by');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'homework_id', 'id');
    }
}
