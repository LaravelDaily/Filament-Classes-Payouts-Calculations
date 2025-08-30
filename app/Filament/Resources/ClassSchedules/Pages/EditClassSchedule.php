<?php

namespace App\Filament\Resources\ClassSchedules\Pages;

use App\Filament\Resources\ClassSchedules\ClassScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClassSchedule extends EditRecord
{
    protected static string $resource = ClassScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
