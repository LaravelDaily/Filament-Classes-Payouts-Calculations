<?php

namespace App\Filament\Resources\WeeklySchedules\Schemas;

use App\Filament\Schemas\Components\TimeSelect;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WeeklyScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Schedule Details')
                    ->schema([
                        Select::make('course_id')
                            ->label('Course')
                            ->relationship('course', 'name')
                            ->required()
                            ->searchable(),

                        Grid::make(3)
                            ->schema([
                                Select::make('day_of_week')
                                    ->label('Day of Week')
                                    ->options([
                                        1 => 'Monday',
                                        2 => 'Tuesday',
                                        3 => 'Wednesday',
                                        4 => 'Thursday',
                                        5 => 'Friday',
                                        6 => 'Saturday',
                                        7 => 'Sunday',
                                    ])
                                    ->required(),
                                TimeSelect::make('start_time')
                                    ->label('Start Time')
                                    ->required(),
                                TimeSelect::make('end_time')
                                    ->label('End Time')
                                    ->required(),
                            ]),
                    ]),

            ]);
    }
}
