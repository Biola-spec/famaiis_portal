<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'created_by'];

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id')->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'group_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
