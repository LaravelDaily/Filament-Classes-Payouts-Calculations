<?php

namespace App\Filament\Resources\ClassSchedules;

use App\Filament\Pages\ManageAttendance;
use App\Filament\Resources\ClassSchedules\Pages\CreateClassSchedule;
use App\Filament\Resources\ClassSchedules\Pages\EditClassSchedule;
use App\Filament\Resources\ClassSchedules\Pages\ListClassSchedules;
use App\Filament\Resources\ClassSchedules\Schemas\ClassScheduleForm;
use App\Filament\Resources\ClassSchedules\Tables\ClassSchedulesTable;
use App\Models\ClassSchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ClassScheduleResource extends Resource
{
    protected static ?string $model = ClassSchedule::class;

    protected static ?string $navigationLabel = 'Class Schedules';

    protected static string|UnitEnum|null $navigationGroup = 'Class Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    public static function form(Schema $schema): Schema
    {
        return ClassScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassSchedulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClassSchedules::route('/'),
            'create' => CreateClassSchedule::route('/create'),
            'edit' => EditClassSchedule::route('/{record}/edit'),
            'attendance' => ManageAttendance::route('/{record}/attendance'),
        ];
    }
}
