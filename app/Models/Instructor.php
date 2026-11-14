<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instructor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'title',
        'expertise',
        'department',
        'departments',
        'department_role',
        'avatar',
        'bio',
        'email',
        'phone',
        'order',
        'is_active',
        'user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'departments' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function ($instructor) {
            if (empty($instructor->user_id) || !User::where('id', $instructor->user_id)->exists()) {
                $user = null;
                if (!empty($instructor->email)) {
                    $user = User::where('email', $instructor->email)->first();
                }
                if (!$user) {
                    $email = !empty($instructor->email) ? $instructor->email : 'faculty_' . time() . '_' . rand(100, 999) . '@cab-academy.com';
                    $userPayload = [
                        'name' => $instructor->name,
                        'password' => bcrypt('CabAcademy@2026'),
                        'role' => 'instructor',
                        'profile_picture' => $instructor->avatar,
                        'email_verified_at' => now(),
                    ];
                    if ($instructor->phone && \Illuminate\Support\Facades\Schema::hasColumn('users', 'phone')) {
                        $userPayload['phone'] = $instructor->phone;
                    }
                    $user = User::withoutEvents(function () use ($email, $userPayload) {
                        return User::firstOrCreate(['email' => $email], $userPayload);
                    });
                }
                $instructor->user_id = $user->id;
                if (empty($instructor->email)) {
                    $instructor->email = $user->email;
                }
            }
        });

        static::saved(function ($instructor) {
            $user = null;
            if ($instructor->user_id) {
                $user = User::find($instructor->user_id);
            }

            // If instructor email points to an existing user, cleanly relink to prevent duplicate key crashes
            if (!empty($instructor->email)) {
                $userWithEmail = User::where('email', $instructor->email)->first();
                if ($userWithEmail && (!$user || $user->id !== $userWithEmail->id)) {
                    $instructor->user_id = $userWithEmail->id;
                    $instructor->saveQuietly();
                    $user = $userWithEmail;
                }
            }

            if ($user) {
                $dirty = false;
                if ($user->name !== $instructor->name) {
                    $user->name = $instructor->name;
                    $dirty = true;
                }
                if ($instructor->email && $user->email !== $instructor->email) {
                    $otherUser = User::where('email', $instructor->email)->where('id', '!=', $user->id)->first();
                    if (!$otherUser) {
                        $user->email = $instructor->email;
                        $dirty = true;
                    }
                }
                if ($instructor->avatar && $user->profile_picture !== $instructor->avatar) {
                    $user->profile_picture = $instructor->avatar;
                    $dirty = true;
                }
                if ($instructor->phone && \Illuminate\Support\Facades\Schema::hasColumn('users', 'phone') && $user->phone !== $instructor->phone) {
                    $user->phone = $instructor->phone;
                    $dirty = true;
                }
                if ($user->role !== 'admin' && $user->role !== 'instructor') {
                    $user->role = 'instructor';
                    $dirty = true;
                }
                if ($dirty) {
                    try {
                        $user->saveQuietly();
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("Could not sync user profile from instructor: " . $e->getMessage());
                    }
                }
            }
        });
    }

    /**
     * Determine whether this instructor profile has been customized beyond the default seeded placeholder.
     */
    public function isCustomized(): bool
    {
        $avatar = trim((string) ($this->avatar ?: ($this->user?->profile_picture ?? '')));
        if ($avatar !== '') {
            return true;
        }
        $name = trim((string) $this->name);
        return $name !== '' && !str_starts_with($name, 'Faculty Chair');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id', 'user_id');
    }

    /**
     * Get fully-qualified avatar image URL with reliable storage prefixing, fallback to user profile picture, and UI Avatars.
     */
    public function isDepartmentHead(): bool
    {
        return ($this->department_role ?? '') === 'head'
            || in_array('head', $this->department_roles_map, true);
    }

    /**
     * Normalized map of [departmentName => 'head' | 'team_member'].
     */
    public function getDepartmentRolesMapAttribute(): array
    {
        $map = [];
        $raw = is_array($this->departments) ? $this->departments : [];

        foreach ($raw as $key => $val) {
            if (is_string($key) && in_array($val, ['head', 'team_member'], true)) {
                $dept = trim($key);
                if ($dept !== '') {
                    $map[$dept] = $val;
                }
            } elseif (is_string($val)) {
                $dept = trim($val);
                if ($dept !== '' && !isset($map[$dept])) {
                    $map[$dept] = 'team_member';
                }
            }
        }

        $primary = trim((string) ($this->department ?? ''));
        if ($primary !== '' && !isset($map[$primary])) {
            $map[$primary] = ($this->department_role === 'head') ? 'head' : 'team_member';
        }

        return $map;
    }

    /**
     * Return all department names this instructor belongs to (primary + additional teams).
     */
    public function getAllDepartmentsAttribute(): array
    {
        return array_keys($this->department_roles_map);
    }

    /**
     * Get this instructor's role in a specific department ('head', 'team_member', or null).
     */
    public function roleInDepartment(string $departmentName): ?string
    {
        return $this->department_roles_map[trim($departmentName)] ?? null;
    }

    /**
     * Check whether this instructor is the Department Head of a specific department.
     */
    public function isHeadOfDepartment(string $departmentName): bool
    {
        return $this->roleInDepartment($departmentName) === 'head';
    }

    /**
     * Check whether this instructor is a member of the given department team.
     */
    public function belongsToDepartment(string $departmentName): bool
    {
        return $this->roleInDepartment($departmentName) !== null;
    }

    public function getAvatarUrlAttribute(): string
    {
        $pic = $this->avatar ?: ($this->user?->profile_picture ?? null);
        $default = 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=051c45&color=fff';
        if (!$pic) {
            return $default;
        }
        return storage_asset($pic, $default);
    }
}
