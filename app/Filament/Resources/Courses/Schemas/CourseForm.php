<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Models\ClassType;
use App\Models\Student;
use App\Models\User;
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
                    ->relationship('students', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Student $record) => $record->name)
                    ->options(function () {
                        return Student::query()
                            ->orderByRaw("SUBSTRING_INDEX(name, ' ', -1), name")
                            ->pluck('name', 'id');
                    })
                    ->columns(4)
                    ->columnSpanFull()
                    ->extraAttributes([
                        'class' => 'compact-checkbox-list',
                    ]),
            ]);
    }
}
