<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WeeklySchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'weeklySchedules';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Schedule Details')
                    ->schema([
                        Select::make('course_id')
                            ->label('Course')
                            ->default(fn (self $livewire) => $livewire->getOwnerRecord()->getKey())
                            ->disabled()
                            ->dehydrated()
                            ->relationship('course', 'name')
                            ->required(),

                        Grid::make(3)
                            ->schema([
                                Select::make('day_of_week')
                                    ->label('Day of Week')
                                    ->options([
                                        1 => 'Monday',
                                        2 => 'Tuesday',
                                        3 => 'Wednesday',
                                        4 => 'Thursday',
                                        5 => 'Friday',
                                        6 => 'Saturday',
                                        7 => 'Sunday',
                                    ])
                                    ->required(),
                                Select::make('start_time')
                                    ->label('Start Time')
                                    ->options($this->getTimeOptions())
                                    ->required(),
                                Select::make('end_time')
                                    ->label('End Time')
                                    ->options($this->getTimeOptions())
                                    ->required(),
                            ]),
                    ]),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day_name')
                    ->label('Day')
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Start')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('End')
                    ->time('H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('day_of_week')
                    ->label('Day of Week')
                    ->options([
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        7 => 'Sunday',
                    ]),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Create')
                    ->modalHeading('Create Weekly Schedule')
                    ->schema($this->form(new Schema)->getComponents())
                    ->action(function (array $data) {
                        $this->getOwnerRecord()->weeklySchedules()->create($data);
                    }),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->modalHeading('Edit Weekly Schedule')
                    ->fillForm(fn ($record) => $record->toArray())
                    ->schema($this->form(new Schema)->getComponents())
                    ->action(function (array $data, $record) {
                        $record->update($data);
                    }),
                Action::make('delete')
                    ->label('Delete')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->delete()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getTimeOptions(): array
    {
        $options = [];

        for ($hour = 7; $hour <= 23; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) {
                $timeKey = sprintf('%02d:%02d:00', $hour, $minute);
                $timeLabel = sprintf('%02d:%02d', $hour, $minute);
                $options[$timeKey] = $timeLabel;
            }
        }

        return $options;
    }
}
