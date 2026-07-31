<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'year_id',
        'class_id',
        'section_id',
        'date',
        'attend_status',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function student_class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    public function year()
    {
        return $this->belongsTo(StudentYear::class, 'year_id');
    }
}
