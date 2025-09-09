<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('classType.name')
                    ->label('Class Type')
                    ->sortable(),
                    
                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->sortable(),
                    
                TextColumn::make('price_per_student')
                    ->label('Price per Student')
                    ->money('USD')
                    ->sortable(),
                    
                TextColumn::make('students_count')
                    ->label('Enrolled Students')
                    ->counts('students')
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('attendance')
                    ->label('Attendance')
                    ->icon(Heroicon::ClipboardDocumentCheck)
                    ->color('success')
                    ->url(fn ($record) => CourseResource::getUrl('attendance', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
