<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSchedule extends Model
{
    /** @use HasFactory<\Database\Factories\ClassScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'learning_class_id',
        'scheduled_date',
        'start_time',
        'end_time',
        'teacher_id',
        'substitute_teacher_id',
        'student_count',
        'teacher_base_pay',
        'teacher_bonus_pay',
        'teacher_total_pay',
        'substitute_base_pay',
        'substitute_bonus_pay',
        'substitute_total_pay',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'teacher_base_pay' => 'decimal:2',
            'teacher_bonus_pay' => 'decimal:2',
            'teacher_total_pay' => 'decimal:2',
            'substitute_base_pay' => 'decimal:2',
            'substitute_bonus_pay' => 'decimal:2',
            'substitute_total_pay' => 'decimal:2',
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

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

}
