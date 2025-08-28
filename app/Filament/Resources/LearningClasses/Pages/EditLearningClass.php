<?php

namespace App\Filament\Resources\LearningClasses\Pages;

use App\Filament\Resources\LearningClasses\LearningClassResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLearningClass extends EditRecord
{
    protected static string $resource = LearningClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
