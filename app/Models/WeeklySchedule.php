<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeeklySchedule extends Model
{
    /** @use HasFactory<\Database\Factories\WeeklyScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'learning_class_id',
        'teacher_id',
        'substitute_teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        'expected_student_count',
        'teacher_base_pay',
        'teacher_bonus_per_student',
        'substitute_base_pay',
        'substitute_bonus_per_student',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'teacher_base_pay' => 'decimal:2',
            'teacher_bonus_per_student' => 'decimal:2',
            'substitute_base_pay' => 'decimal:2',
            'substitute_bonus_per_student' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function learningClass(): BelongsTo
    {
        return $this->belongsTo(LearningClass::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function substituteTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'substitute_teacher_id');
    }

    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function getDayNameAttribute(): string
    {
        return match($this->day_of_week) {
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
            default => 'Unknown',
        };
    }
}
