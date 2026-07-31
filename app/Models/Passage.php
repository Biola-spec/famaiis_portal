<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passage extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'content',
        'image',
        'start_number',
        'end_number',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
