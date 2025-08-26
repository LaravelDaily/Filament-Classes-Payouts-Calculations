<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningClass extends Model
{
    /** @use HasFactory<\Database\Factories\LearningClassFactory> */
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

    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}
