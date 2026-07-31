<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSection extends Model
{
    use HasFactory;

    protected $table = 'student_section';

    protected $fillable = [
        'student_id',
        'section_id',
        'class_id',
        'year_id',
        'is_active',
        'enrollment_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'enrollment_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function section()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function year()
    {
        return $this->belongsTo(StudentYear::class, 'year_id');
    }
}
