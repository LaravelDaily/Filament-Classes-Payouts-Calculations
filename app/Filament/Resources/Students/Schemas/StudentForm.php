<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                    
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                    
                CheckboxList::make('courses')
                    ->relationship('courses', 'name')
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
