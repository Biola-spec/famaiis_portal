<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSection extends Model
{
    protected $guarded = [];

    // Head teacher of this section
    public function headTeacher()
    {
        return $this->belongsTo(User::class, 'head_teacher_id', 'id');
    }

    // Students enrolled in this section (via pivot)
    public function students()
    {
        return $this->belongsToMany(User::class, 'student_section', 'section_id', 'student_id')
            ->withPivot(['class_id', 'year_id', 'is_active', 'enrollment_date'])
            ->withTimestamps();
    }

    // Active students only
    public function activeStudents()
    {
        return $this->belongsToMany(User::class, 'student_section', 'section_id', 'student_id')
            ->withPivot(['class_id', 'year_id', 'is_active', 'enrollment_date'])
            ->wherePivot('is_active', true)
            ->withTimestamps();
    }

    // Teachers assigned to this section (via pivot)
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'teacher_section', 'section_id', 'teacher_id')
            ->withPivot(['is_active'])
            ->withTimestamps();
    }

    // Subjects belonging to this section
    public function subjects()
    {
        return $this->hasMany(SchoolSubject::class, 'section_id', 'id');
    }

    // Fee structures for this section
    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class, 'section_id', 'id');
    }

    // Student fees for this section
    public function studentFees()
    {
        return $this->hasMany(StudentFee::class, 'section_id', 'id');
    }

    // Payments for this section
    public function feePayments()
    {
        return $this->hasMany(FeePayment::class, 'section_id', 'id');
    }

    // Marking settings for this section
    public function markingSettings()
    {
        return $this->hasMany(ClassMarkingSetting::class, 'section_id', 'id');
    }

    // Classes belonging to this section
    public function classes()
    {
        return $this->hasMany(StudentClass::class, 'section_id', 'id');
    }
}
