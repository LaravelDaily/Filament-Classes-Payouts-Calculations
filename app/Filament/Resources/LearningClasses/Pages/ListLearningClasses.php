<?php

namespace App\Filament\Resources\LearningClasses\Pages;

use App\Filament\Resources\LearningClasses\LearningClassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLearningClasses extends ListRecords
{
    protected static string $resource = LearningClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
