<?php

namespace App\Filament\Resources\ClassSchedules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClassScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Schedule Details')
                    ->schema([
                        Select::make('learning_class_id')
                            ->label('Learning Class')
                            ->relationship('learningClass', 'name')
                            ->required()
                            ->searchable(),
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('scheduled_date')
                                    ->required()
                                    ->native(false),
                                TextInput::make('student_count')
                                    ->label('Student Count')
                                    ->numeric()
                                    ->default(0),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TimePicker::make('start_time')
                                    ->required()
                                    ->native(false),
                                TimePicker::make('end_time')
                                    ->required()
                                    ->native(false),
                            ]),
                    ]),

                Section::make('Teachers')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('teacher_id')
                                    ->label('Primary Teacher')
                                    ->relationship('teacher', 'name')
                                    ->required()
                                    ->searchable(),
                                Select::make('substitute_teacher_id')
                                    ->label('Substitute Teacher')
                                    ->relationship('substituteTeacher', 'name')
                                    ->searchable()
                                    ->nullable(),
                            ]),
                    ]),

                Section::make('Payout Calculations')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('teacher_base_pay')
                                    ->label('Teacher Base Pay')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled(),
                                TextInput::make('teacher_bonus_pay')
                                    ->label('Teacher Bonus Pay')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled(),
                                TextInput::make('teacher_total_pay')
                                    ->label('Teacher Total Pay')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled(),
                            ]),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('substitute_base_pay')
                                    ->label('Substitute Base Pay')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled(),
                                TextInput::make('substitute_bonus_pay')
                                    ->label('Substitute Bonus Pay')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled(),
                                TextInput::make('substitute_total_pay')
                                    ->label('Substitute Total Pay')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled(),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
