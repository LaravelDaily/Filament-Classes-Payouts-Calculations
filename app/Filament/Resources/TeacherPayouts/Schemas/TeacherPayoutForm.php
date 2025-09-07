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
                                Select::make('teacher_id')
                                    ->label('Teacher')
                                    ->relationship('teacher', 'name')
                                    ->searchable()
                                    ->required(),
                                TextInput::make('month')
                                    ->label('Month (YYYY-MM)')
                                    ->placeholder('2024-01')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Payment Details')
                    ->schema([
                        TextInput::make('total_pay')
                            ->label('Total Pay')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.0)
                            ->required(),
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
