<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory;

    protected $fillable = [
        'class_type_id',
        'name',
        'description',
        'price_per_student',
    ];

    protected function casts(): array
    {
        return [
            'price_per_student' => 'decimal:2',
        ];
    }

    public function classType(): BelongsTo
    {
        return $this->belongsTo(ClassType::class);
    }

    public function courseClasses(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function weeklySchedules(): HasMany
    {
        return $this->hasMany(WeeklySchedule::class);
    }
}
