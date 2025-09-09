<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('class_type_id')
                    ->label('Class Type')
                    ->relationship('classType', 'name')
                    ->required(),

                Select::make('teacher_id')
                    ->label('Teacher')
                    ->relationship('teacher', 'name')
                    ->required(),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->columnSpanFull(),

                TextInput::make('price_per_student')
                    ->label('Price per Student')
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01),

                CheckboxList::make('students')
                    ->relationship('students', 'first_name', function ($query) {
                        return $query->orderBy('last_name')->orderBy('first_name');
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                    ->columns(4)
                    ->columnSpanFull(),
            ]);
    }
}
