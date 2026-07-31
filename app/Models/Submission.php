<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $guarded = [];

    public function homework()
    {
        return $this->belongsTo(Homework::class, 'homework_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }
}
