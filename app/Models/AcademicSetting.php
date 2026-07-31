<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'current_session_id',
        'current_term',
        'assessment_areas',
    ];

    protected $casts = [
        'assessment_areas' => 'array',
    ];

    public function currentSession()
    {
        return $this->belongsTo(StudentYear::class, 'current_session_id');
    }

}
