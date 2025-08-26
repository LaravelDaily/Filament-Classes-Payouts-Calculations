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
        'base_pay',
        'bonus_pay',
        'total_pay',
    ];

    protected function casts(): array
    {
        return [
            'base_pay' => 'decimal:2',
            'bonus_pay' => 'decimal:2',
            'total_pay' => 'decimal:2',
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
