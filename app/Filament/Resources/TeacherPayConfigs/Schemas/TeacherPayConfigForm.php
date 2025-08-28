<?php

namespace App\Filament\Resources\TeacherPayConfigs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeacherPayConfigForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Teacher Pay Configuration')
                    ->description('Configure base pay and bonus rates for teachers')
                    ->components([
                        Select::make('user_id')
                            ->label('Teacher')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('base_pay')
                            ->label('Base Pay')
                            ->prefix('$')
                            ->numeric()
                            ->step(0.01)
                            ->required()
                            ->helperText('Fixed amount paid per class regardless of student count'),

                        TextInput::make('bonus_per_student')
                            ->label('Bonus per Student')
                            ->prefix('$')
                            ->numeric()
                            ->step(0.01)
                            ->required()
                            ->helperText('Additional amount paid for each enrolled student'),
                    ]),
            ]);
    }
}
