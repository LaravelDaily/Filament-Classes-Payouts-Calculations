<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassType extends Model
{
    /** @use HasFactory<\Database\Factories\ClassTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function learningClasses(): HasMany
    {
        return $this->hasMany(LearningClass::class);
    }
}
