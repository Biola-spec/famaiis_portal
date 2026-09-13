<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'report_type',
        'title',
        'description',
        'video_path',
        'video_thumbnail',
        'is_for_all',
        'status',
        'approved_by',
        'approved_at',
        'recalled_by',
        'recalled_at',
        'approval_note',
    ];

    protected $casts = [
        'is_for_all' => 'boolean',
        'approved_at' => 'datetime',
        'recalled_at' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(SchoolSubject::class, 'subject_id');
    }

    public function media()
    {
        return $this->hasMany(ReportMedia::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function recalledBy()
    {
        return $this->belongsTo(User::class, 'recalled_by');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'report_student', 'report_id', 'student_id')
                    ->withPivot('seen_at')
                    ->withTimestamps();
    }
}
