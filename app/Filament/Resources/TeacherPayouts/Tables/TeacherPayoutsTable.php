<?php

namespace App\Filament\Resources\TeacherPayouts\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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
                TextColumn::make('month')
                    ->label('Month')
                    ->searchable()
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
                Filter::make('is_paid')
                    ->query(fn (Builder $query): Builder => $query->where('is_paid', true))
                    ->label('Paid Only'),
                Filter::make('unpaid')
                    ->query(fn (Builder $query): Builder => $query->where('is_paid', false))
                    ->label('Unpaid Only'),
            ])
            ->recordActions(
                auth()->user()?->isAdmin()
                    ? [EditAction::make()]
                    : []
            )
            ->toolbarActions(
                auth()->user()?->isAdmin()
                    ? [
                        BulkActionGroup::make([
                            BulkAction::make('markAsPaid')
                                ->label('Mark as Paid')
                                ->icon('heroicon-o-check-circle')
                                ->color('success')
                                ->requiresConfirmation()
                                ->action(function (Collection $records): void {
                                    $now = now();

                                    $records->each(function ($record) use ($now) {
                                        if (! $record->is_paid) {
                                            $record->update([
                                                'is_paid' => true,
                                                'paid_at' => $now,
                                            ]);
                                        }
                                    });
                                })
                                ->deselectRecordsAfterCompletion(),
                            DeleteBulkAction::make(),
                        ]),
                    ]
                    : []
            );
    }
}
