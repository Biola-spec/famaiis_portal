<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatConnection extends Model
{
    protected $fillable = ['user_id', 'connected_user_id', 'connected_by'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function connectedUser()
    {
        return $this->belongsTo(User::class, 'connected_user_id');
    }

    public function connectedBy()
    {
        return $this->belongsTo(User::class, 'connected_by');
    }

    public static function areConnected(int $firstUserId, int $secondUserId): bool
    {
        [$low, $high] = [min($firstUserId, $secondUserId), max($firstUserId, $secondUserId)];

        return static::where('user_id', $low)->where('connected_user_id', $high)->exists();
    }

    public static function connectedUserIds(int $userId): array
    {
        return static::where('user_id', $userId)->pluck('connected_user_id')
            ->merge(static::where('connected_user_id', $userId)->pluck('user_id'))
            ->unique()->values()->all();
    }

    public static function connect(int $firstUserId, int $secondUserId, int $adminId): self
    {
        [$low, $high] = [min($firstUserId, $secondUserId), max($firstUserId, $secondUserId)];

        return static::updateOrCreate(
            ['user_id' => $low, 'connected_user_id' => $high],
            ['connected_by' => $adminId]
        );
    }
}
