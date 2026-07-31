<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentMarks extends Model
{
    protected $fillable = [
        'student_id',
        'section_id',
        'id_no',
        'year_id',
        'session_id',
        'class_id',
        'assign_subject_id',
        'subject_id',
        'term',
        'term_id',
        'marks',
        'ca_score',
        'exam_score',
        'project_score',
        'ca_breakdown',
        'total_score',
        'grade',
    ];

    protected $casts = [
        'ca_breakdown' => 'array',
    ];

    public function student(){
    	return $this->belongsTo(User::class, 'student_id','id');
    }
 
 public function assign_subject(){
    	return $this->belongsTo(AssignSubject::class, 'assign_subject_id','id');
    }

 public function year(){
    	return $this->belongsTo(StudentYear::class, 'year_id','id');
    }

 public function student_class(){
    	return $this->belongsTo(StudentClass::class, 'class_id','id');
    }

 public function subject(){
        return $this->belongsTo(SchoolSubject::class, 'subject_id','id');
   }


    public function section(){
        return $this->belongsTo(SchoolSection::class, 'section_id','id');
    }

    public function exam_type()
    {
        return $this->belongsTo(Term::class, 'term_id', 'id');
    }
}
