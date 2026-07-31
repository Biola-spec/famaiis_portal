<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Designation;
use App\Models\Message;
use App\Models\ChatGroup;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'language',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function designation(){
        return $this->belongsTo(Designation::class, 'designation_id','id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(...$roles)
    {
        foreach ($roles as $role) {
            $checkRole = strtolower($role);
            
            // 1. Check Many-to-Many Roles
            if ($this->roles->contains(fn($r) => strtolower($r->name) === $checkRole)) {
                return true;
            }

            // 2. Check 'role' column
            if (strtolower($this->role ?? '') === $checkRole) {
                return true;
            }

            // 3. Check 'usertype' column (Legacy)
            if (strtolower($this->usertype ?? '') === $checkRole) {
                return true;
            }
            
            // 4. Super Admin bypass (Super Admin passes all role checks except for Parent/Student if used for exclusion)
            if ($checkRole !== 'parent' && $checkRole !== 'student') {
                if ($this->roles->contains(fn($r) => strtolower($r->name) === 'super admin')) {
                    return true;
                }
            }
        }
        return false;
    }

    public function hasPermission($permission)
    {
        // Admin/Super Admin bypass
        if ($this->hasRole('Admin', 'Super Admin')) {
            return true;
        }

        // Check assigned roles' permissions (Many-to-Many)
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('slug', $permission)) {
                return true;
            }
        }

        // Check permissions for legacy role columns (role, usertype)
        static $legacyRolesCache = [];
        $cacheKey = $this->id;

        if (!array_key_exists($cacheKey, $legacyRolesCache)) {
            $legacyRoleNames = array_filter([$this->role, $this->usertype]);
            if (!empty($legacyRoleNames)) {
                $legacyRolesCache[$cacheKey] = \App\Models\Role::with('permissions')
                    ->whereIn('name', $legacyRoleNames)
                    ->get();
            } else {
                $legacyRolesCache[$cacheKey] = collect();
            }
        }

        foreach ($legacyRolesCache[$cacheKey] as $role) {
            if ($role->permissions->contains('slug', $permission)) {
                return true;
            }
        }

        return false;
    }

    public function children()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_id', 'student_id');
    }

    public function parents()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id');
    }

    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class, 'teacher_id');
    }

    // Legacy single section (primary default section)
    public function section(): BelongsTo
    {
        return $this->belongsTo(SchoolSection::class, 'section_id', 'id');
    }

    // Multi-section: sections a student is enrolled in
    public function sections()
    {
        return $this->belongsToMany(SchoolSection::class, 'student_section', 'student_id', 'section_id')
            ->withPivot(['class_id', 'year_id', 'is_active', 'enrollment_date'])
            ->withTimestamps();
    }

    // Multi-section: active enrollments only
    public function activeSections()
    {
        return $this->belongsToMany(SchoolSection::class, 'student_section', 'student_id', 'section_id')
            ->withPivot(['class_id', 'year_id', 'is_active', 'enrollment_date'])
            ->wherePivot('is_active', true)
            ->withTimestamps();
    }

    // Multi-section: sections a teacher is assigned to
    public function teacherSections()
    {
        return $this->belongsToMany(SchoolSection::class, 'teacher_section', 'teacher_id', 'section_id')
            ->withPivot(['is_active'])
            ->withTimestamps();
    }

    // Student fee records
    public function studentFees(): HasMany
    {
        return $this->hasMany(StudentFee::class, 'student_id');
    }

    // Fee payment records
    public function feePayments(): HasMany
    {
        return $this->hasMany(FeePayment::class, 'student_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    public function initiatedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'paid_by_user_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function chatGroups()
    {
        return $this->belongsToMany(ChatGroup::class, 'group_members', 'user_id', 'group_id')->withTimestamps();
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function resultLinks(): HasMany
    {
        return $this->hasMany(ParentResultLink::class, 'parent_id');
    }
}
