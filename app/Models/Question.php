<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'correct_answer',
        'image',
        'image_a',
        'image_b',
        'image_c',
        'image_d',
        'image_e',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
