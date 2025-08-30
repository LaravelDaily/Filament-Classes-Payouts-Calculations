<?php

namespace App\Filament\Resources\ClassSchedules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class ClassScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('learning_class_id')
                    ->label('Learning Class')
                    ->relationship('learningClass', 'name')
                    ->required()
                    ->searchable(),
                DatePicker::make('scheduled_date')
                    ->required()
                    ->native(false),
                TimePicker::make('start_time')
                    ->required()
                    ->native(false),
                TimePicker::make('end_time')
                    ->required()
                    ->native(false),
                Select::make('teacher_id')
                    ->label('Teacher')
                    ->relationship('teacher', 'name')
                    ->required()
                    ->searchable(),
                Select::make('substitute_teacher_id')
                    ->label('Substitute Teacher')
                    ->relationship('substituteTeacher', 'name')
                    ->searchable()
                    ->nullable(),
            ]);
    }
}
