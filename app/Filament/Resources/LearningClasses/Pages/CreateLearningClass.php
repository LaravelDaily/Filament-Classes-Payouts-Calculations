<?php

namespace App\Filament\Resources\LearningClasses\Pages;

use App\Filament\Resources\LearningClasses\LearningClassResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLearningClass extends CreateRecord
{
    protected static string $resource = LearningClassResource::class;
}
