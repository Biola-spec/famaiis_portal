<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ParentResultLink extends Model
{
    protected $fillable = [
        'token',
        'parent_id',
        'student_id',
        'year_id',
        'term',
        'created_by',
        'expires_at',
        'is_active',
        'access_count',
        'last_accessed_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function year(): BelongsTo
    {
        return $this->belongsTo(StudentYear::class, 'year_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function recordAccess(): void
    {
        $this->increment('access_count');
        $this->forceFill(['last_accessed_at' => now()])->save();
    }

    public function shortUrl(): string
    {
        return url('/r/'.$this->token);
    }

    public static function generateUniqueToken(): string
    {
        do {
            $token = strtolower(Str::random(8));
        } while (static::where('token', $token)->exists());

        return $token;
    }

    public static function findValidByToken(string $token): ?self
    {
        $link = static::with(['parent.children', 'student', 'year'])->where('token', $token)->first();

        if (!$link || !$link->isValid()) {
            return null;
        }

        return $link;
    }

    public function allowedStudentIds(): array
    {
        return $this->parent?->children()->pluck('id')->all() ?? [];
    }

    public function resolveStudentId(?int $requestedStudentId = null): ?int
    {
        $allowed = $this->allowedStudentIds();

        if ($this->student_id) {
            return in_array((int) $this->student_id, $allowed, true) ? (int) $this->student_id : null;
        }

        if ($requestedStudentId && in_array((int) $requestedStudentId, $allowed, true)) {
            return (int) $requestedStudentId;
        }

        return $allowed[0] ?? null;
    }
}
