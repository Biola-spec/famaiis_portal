<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $guarded = [];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }
}
