<?php

namespace App\Filament\Resources\WeeklySchedules;

use App\Filament\Resources\WeeklySchedules\Pages\CreateWeeklySchedule;
use App\Filament\Resources\WeeklySchedules\Pages\EditWeeklySchedule;
use App\Filament\Resources\WeeklySchedules\Pages\ListWeeklySchedules;
use App\Filament\Resources\WeeklySchedules\Schemas\WeeklyScheduleForm;
use App\Filament\Resources\WeeklySchedules\Tables\WeeklySchedulesTable;
use App\Models\WeeklySchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WeeklyScheduleResource extends Resource
{
    protected static ?string $model = WeeklySchedule::class;

    protected static ?string $navigationLabel = 'Weekly Schedules';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    // Hide from sidebar navigation – managed as a nested relation
    protected static bool $shouldRegisterNavigation = false;

    // Kept for completeness, but ignored due to shouldRegisterNavigation=false
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return WeeklyScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WeeklySchedulesTable::configure($table);
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
            'index' => ListWeeklySchedules::route('/'),
            'create' => CreateWeeklySchedule::route('/create'),
            'edit' => EditWeeklySchedule::route('/{record}/edit'),
        ];
    }
}
