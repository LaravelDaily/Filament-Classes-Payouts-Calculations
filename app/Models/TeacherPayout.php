<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherPayout extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherPayoutFactory> */
    use HasFactory;

    protected $fillable = [
        'class_schedule_id',
        'teacher_id',
        'month',
        'student_count',
        'is_substitute',
        'base_pay',
        'bonus_pay',
        'total_pay',
        'is_paid',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'base_pay' => 'decimal:2',
            'bonus_pay' => 'decimal:2',
            'total_pay' => 'decimal:2',
            'is_substitute' => 'boolean',
            'is_paid' => 'boolean',
            'paid_at' => 'datetime',
        ];
    }

    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
