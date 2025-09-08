<?php

namespace App\Filament\Resources\WeeklySchedules\Pages;

use App\Filament\Resources\WeeklySchedules\WeeklyScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWeeklySchedule extends EditRecord
{
    protected static string $resource = WeeklyScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

