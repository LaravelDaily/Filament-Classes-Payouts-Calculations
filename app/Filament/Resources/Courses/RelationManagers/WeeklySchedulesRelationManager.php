<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Enums\DayOfWeek;
use App\Filament\Schemas\Components\TimeSelect;
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
                                    ->options(DayOfWeek::options())
                                    ->required(),
                                TimeSelect::make('start_time')
                                    ->label('Start Time')
                                    ->required(),
                                TimeSelect::make('end_time')
                                    ->label('End Time')
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
                    ->options(DayOfWeek::options()),
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
            ->recordActions([
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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
