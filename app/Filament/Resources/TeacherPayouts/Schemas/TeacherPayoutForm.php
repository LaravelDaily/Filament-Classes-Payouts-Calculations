<?php

namespace App\Filament\Resources\TeacherPayouts\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeacherPayoutForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payout Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('class_schedule_id')
                                    ->label('Class Schedule')
                                    ->relationship(
                                        'classSchedule',
                                        'id',
                                        modifyQueryUsing: fn ($query) => $query->with(['learningClass', 'teacher'])
                                    )
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->learningClass->name} - {$record->scheduled_date->format('M d, Y')} - {$record->teacher->name}"
                                    )
                                    ->searchable()
                                    ->required(),
                                Select::make('teacher_id')
                                    ->label('Teacher')
                                    ->relationship('teacher', 'name')
                                    ->searchable()
                                    ->required(),
                            ]),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('month')
                                    ->label('Month (YYYY-MM)')
                                    ->placeholder('2024-01')
                                    ->required(),
                                TextInput::make('student_count')
                                    ->label('Students Attended')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                Checkbox::make('is_substitute')
                                    ->label('Substitute Teacher'),
                            ]),
                    ]),

                Section::make('Payment Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('base_pay')
                                    ->label('Base Pay')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(fn () => config('teacher_pay.base_pay', 50.00))
                                    ->required(),
                                TextInput::make('bonus_pay')
                                    ->label('Bonus Pay')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0.0)
                                    ->required(),
                                TextInput::make('total_pay')
                                    ->label('Total Pay')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0.0)
                                    ->required(),
                            ]),
                    ]),

                Section::make('Payment Status')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Checkbox::make('is_paid')
                                    ->label('Marked as Paid'),
                                DateTimePicker::make('paid_at')
                                    ->label('Paid At')
                                    ->visible(fn ($get) => $get('is_paid')),
                            ]),
                    ]),
            ]);
    }
}
