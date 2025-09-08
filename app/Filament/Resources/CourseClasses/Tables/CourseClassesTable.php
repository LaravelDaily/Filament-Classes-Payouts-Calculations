<?php

namespace App\Filament\Resources\CourseClasses\Tables;

use App\Services\ScheduleGenerationService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CourseClassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.name')
                    ->label('Course')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('scheduled_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('end_time')
                    ->time('H:i')
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
                TextColumn::make('student_count')
                    ->label('Students')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('teacher_total_pay')
                    ->label('Teacher Pay')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('substitute_total_pay')
                    ->label('Substitute Pay')
                    ->money('USD')
                    ->sortable()
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
                SelectFilter::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'name'),
                SelectFilter::make('teacher_id')
                    ->label('Teacher')
                    ->relationship('teacher', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->headerActions([
                Action::make('generate_monthly_schedules')
                    ->label('Generate Monthly Schedules')
                    ->color('success')
                    ->icon('heroicon-o-calendar-days')
                    ->modalHeading('Generate Class Schedules for Month')
                    ->modalDescription('Generate class schedules based on weekly schedule patterns. Note: Schedules can only be generated once per month.')
                    ->schema([
                        Select::make('month_year')
                            ->label('Select Month')
                            ->options(function () {
                                $service = new ScheduleGenerationService;

                                return $service->getAvailableMonthsForGeneration()
                                    ->pluck('label', 'value')
                                    ->toArray();
                            })
                            ->placeholder('Choose a month...')
                            ->required()
                            ->helperText('Only months without existing generated schedules are shown'),
                    ])
                    ->action(function (array $data) {
                        try {
                            [$year, $month] = explode('-', $data['month_year']);
                            $service = new ScheduleGenerationService;

                            $createdSchedules = $service->generateMonthlySchedules((int) $year, (int) $month);

                            $monthName = \Carbon\Carbon::create($year, $month, 1)->format('F Y');

                            Notification::make()
                                ->title('Schedules Generated Successfully!')
                                ->body(sprintf('Created %d class schedules for %s.', count($createdSchedules), $monthName))
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Generation Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('scheduled_date', 'asc');
    }
}
