<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'student_year_id', 'session_id', 'is_active'];

    public function academic_year()
    {
        return $this->belongsTo(StudentYear::class, 'student_year_id', 'id');
    }

    public function session()
    {
        return $this->belongsTo(StudentYear::class, 'session_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
