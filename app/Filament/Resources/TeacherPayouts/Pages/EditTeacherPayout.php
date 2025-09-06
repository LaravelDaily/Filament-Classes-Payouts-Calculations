<?php

namespace App\Filament\Resources\TeacherPayouts\Pages;

use App\Filament\Resources\TeacherPayouts\TeacherPayoutResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeacherPayout extends EditRecord
{
    protected static string $resource = TeacherPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
