<?php

namespace App\Filament\Resources\WeeklySchedules\Pages;

use App\Filament\Resources\WeeklySchedules\WeeklyScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWeeklySchedules extends ListRecords
{
    protected static string $resource = WeeklyScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

