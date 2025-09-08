<?php

namespace App\Filament\Resources\Enrollments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Enrollment Details')
                    ->schema([
                        Select::make('student_id')
                            ->relationship('student', 'name')
                            ->required()
                            ->searchable(),
                        Select::make('course_id')
                            ->relationship('course', 'name')
                            ->required()
                            ->searchable(),
                        DatePicker::make('start_date')
                            ->required(),
                        DatePicker::make('end_date'),
                    ]),
            ]);
    }
}
