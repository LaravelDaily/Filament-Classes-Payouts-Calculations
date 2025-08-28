<?php

namespace App\Filament\Resources\TeacherPayConfigs\Pages;

use App\Filament\Resources\TeacherPayConfigs\TeacherPayConfigResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeacherPayConfig extends EditRecord
{
    protected static string $resource = TeacherPayConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
