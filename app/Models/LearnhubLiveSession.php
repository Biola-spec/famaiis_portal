<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LearnhubLiveSession extends Model
{
    protected $fillable = [
        'subject_id',
        'lesson_id',
        'teacher_id',
        'title',
        'room_name',
        'scheduled_at',
        'started_at',
        'ended_at',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(LearnhubSubject::class, 'subject_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(LearnhubLesson::class, 'lesson_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public static function generateRoomName(int $subjectId): string
    {
        return 'famaiis-'.$subjectId.'-'.Str::lower(Str::random(8));
    }

    public function isJoinable(): bool
    {
        return in_array($this->status, ['scheduled', 'live'], true);
    }
}
