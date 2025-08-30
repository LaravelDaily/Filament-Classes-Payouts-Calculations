<?php

namespace App\Filament\Resources\ClassSchedules\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClassSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('learningClass.name')
                    ->label('Learning Class')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('scheduled_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->time()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->time()
                    ->sortable(),
                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('substituteTeacher.name')
                    ->label('Substitute Teacher')
                    ->sortable()
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('learning_class_id')
                    ->label('Learning Class')
                    ->relationship('learningClass', 'name'),
                SelectFilter::make('teacher_id')
                    ->label('Teacher')
                    ->relationship('teacher', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('manage_attendance')
                    ->label('Manage Attendance')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->url(fn ($record) => route('filament.admin.resources.class-schedules.attendance', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('scheduled_date', 'asc');
    }
}
