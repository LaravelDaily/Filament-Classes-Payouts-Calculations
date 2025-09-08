<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

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

                Section::make('Teachers')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('teacher_id')
                                    ->label('Primary Teacher')
                                    ->relationship('teacher', 'name')
                                    ->required()
                                    ->searchable(),
                                Select::make('substitute_teacher_id')
                                    ->label('Substitute Teacher')
                                    ->relationship('substituteTeacher', 'name')
                                    ->searchable()
                                    ->nullable(),
                            ]),
                    ]),

                Section::make('Attendance & Pay Rates')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('expected_student_count')
                                    ->label('Expected Students')
                                    ->numeric()
                                    ->default(0),
                                TextInput::make('teacher_base_pay')
                                    ->label('Teacher Base Pay')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01),
                                TextInput::make('teacher_bonus_per_student')
                                    ->label('Teacher Bonus / Student')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01),
                                TextInput::make('substitute_base_pay')
                                    ->label('Substitute Base Pay')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01),
                                TextInput::make('substitute_bonus_per_student')
                                    ->label('Substitute Bonus / Student')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Active Period')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                                DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->native(false),
                                DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->native(false),
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
                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('expected_student_count')
                    ->label('Expected')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->schema($this->form(new Schema())->getComponents())
                    ->action(function (array $data) {
                        $this->getOwnerRecord()->weeklySchedules()->create($data);
                    }),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->modalHeading('Edit Weekly Schedule')
                    ->fillForm(fn ($record) => $record->toArray())
                    ->schema($this->form(new Schema())->getComponents())
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
