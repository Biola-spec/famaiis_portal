<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'section_id',
        'student_id',
        'subject_id',
        'term',
        'title',
        'duration',
        'retake_limit',
        'created_by',
        'status',
    ];

    public function student_class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(SchoolSubject::class, 'subject_id');
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function passages()
    {
        return $this->hasMany(Passage::class)->orderBy('start_number');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function retakes()
    {
        return $this->hasMany(QuizRetake::class)->orderByDesc('granted_at');
    }
}
