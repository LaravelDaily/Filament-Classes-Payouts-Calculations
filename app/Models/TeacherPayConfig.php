<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherPayConfig extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherPayConfigFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'base_pay',
        'bonus_per_student',
    ];

    protected function casts(): array
    {
        return [
            'base_pay' => 'decimal:2',
            'bonus_per_student' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
