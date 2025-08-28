<?php

namespace App\Filament\Resources\TeacherPayConfigs\Pages;

use App\Filament\Resources\TeacherPayConfigs\TeacherPayConfigResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeacherPayConfigs extends ListRecords
{
    protected static string $resource = TeacherPayConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
