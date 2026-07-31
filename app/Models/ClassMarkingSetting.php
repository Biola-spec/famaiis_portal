<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassMarkingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'section_id',
        'subject_id',
        'session_id',
        'term',
        'ca_count',
        'ca_labels',
        'ca_weights',
        'ca_weight',
        'exam_weight',
        'exam_label',
        'project_enabled',
        'total_score',
        'is_active',
    ];

    protected $casts = [
        'project_enabled' => 'boolean',
        'ca_labels' => 'array',
        'ca_weights' => 'array',
        'is_active' => 'boolean',
    ];


    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    public function subject()
    {
        return $this->belongsTo(SchoolSubject::class, 'subject_id');
    }

    public function session()
    {
        return $this->belongsTo(StudentYear::class, 'session_id');
    }

}
