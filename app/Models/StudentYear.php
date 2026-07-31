<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentYear extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    public function terms()
    {
        return $this->hasMany(Term::class, 'student_year_id', 'id');
    }

    public function asSessionTerms()
    {
        return $this->hasMany(Term::class, 'session_id', 'id');
    }

    protected static function booted()
    {
        static::created(function ($year) {
            $terms = ['First Term', 'Second Term', 'Third Term'];
            foreach ($terms as $termName) {
                Term::create([
                    'name' => $termName,
                    'student_year_id' => $year->id,
                    'session_id' => $year->id,
                    'is_active' => ($termName == 'First Term') ? true : false,
                ]);
            }
        });
    }
}
