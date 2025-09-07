<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeacherPayout extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherPayoutFactory> */
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'month',
        'total_pay',
        'is_paid',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'total_pay' => 'decimal:2',
            'is_paid' => 'boolean',
            'paid_at' => 'datetime',
        ];
    }


    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'teacher_id', 'teacher_id')
            ->whereBetween('scheduled_date', [
                Carbon::createFromFormat('Y-m', $this->month)->startOfMonth(),
                Carbon::createFromFormat('Y-m', $this->month)->endOfMonth(),
            ]);
    }
}
