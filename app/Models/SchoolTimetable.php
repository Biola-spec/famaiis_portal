<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolTimetable extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    public function year() { return $this->belongsTo(StudentYear::class, 'year_id'); }
    public function section() { return $this->belongsTo(SchoolSection::class, 'section_id'); }
    public function studentClass() { return $this->belongsTo(StudentClass::class, 'class_id'); }
    public function subject() { return $this->belongsTo(SchoolSubject::class, 'subject_id'); }
    public function teacher() { return $this->belongsTo(User::class, 'teacher_id'); }
}
