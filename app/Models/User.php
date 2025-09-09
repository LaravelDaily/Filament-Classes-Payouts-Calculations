<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function courseClasses(): HasMany
    {
        return $this->hasMany(CourseClass::class, 'teacher_id');
    }

    public function substituteCourseClasses(): HasMany
    {
        return $this->hasMany(CourseClass::class, 'substitute_teacher_id');
    }

    public function teacherPayouts(): HasMany
    {
        return $this->hasMany(TeacherPayout::class, 'teacher_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function isTeacher(): bool
    {
        return $this->role->name === 'Teacher';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role->name, ['Admin', 'Owner']);
    }
}
