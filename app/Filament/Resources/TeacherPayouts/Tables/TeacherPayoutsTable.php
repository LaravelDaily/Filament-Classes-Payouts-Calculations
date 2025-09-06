<?php

namespace App\Filament\Resources\TeacherPayouts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TeacherPayoutsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classSchedule.learningClass.name')
                    ->label('Class')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classSchedule.scheduled_date')
                    ->label('Class Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('month')
                    ->label('Month')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student_count')
                    ->label('Students')
                    ->alignCenter()
                    ->sortable(),
                IconColumn::make('is_substitute')
                    ->label('Substitute')
                    ->boolean()
                    ->alignCenter(),
                TextColumn::make('base_pay')
                    ->label('Base Pay')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('bonus_pay')
                    ->label('Bonus Pay')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('total_pay')
                    ->label('Total Pay')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold'),
                IconColumn::make('is_paid')
                    ->label('Paid')
                    ->boolean()
                    ->alignCenter(),
                TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('month')
                    ->options(function () {
                        $months = [];
                        $current = now()->startOfMonth();
                        for ($i = -6; $i <= 6; $i++) {
                            $date = $current->copy()->addMonths($i);
                            $months[$date->format('Y-m')] = $date->format('F Y');
                        }

                        return $months;
                    })
                    ->default(now()->format('Y-m')),
                SelectFilter::make('teacher_id')
                    ->relationship('teacher', 'name')
                    ->searchable(),
                Filter::make('is_substitute')
                    ->query(fn (Builder $query): Builder => $query->where('is_substitute', true))
                    ->label('Substitute Teachers Only'),
                Filter::make('is_paid')
                    ->query(fn (Builder $query): Builder => $query->where('is_paid', true))
                    ->label('Paid Only'),
                Filter::make('unpaid')
                    ->query(fn (Builder $query): Builder => $query->where('is_paid', false))
                    ->label('Unpaid Only'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
