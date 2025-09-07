<?php

namespace App\Filament\Resources\TeacherPayouts\Pages;

use App\Filament\Resources\TeacherPayouts\TeacherPayoutResource;
use App\Services\PayoutCalculationService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;

class ListTeacherPayouts extends ListRecords
{
    protected static string $resource = TeacherPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generatePayouts')
                ->label('Generate Monthly Payouts')
                ->icon('heroicon-o-currency-dollar')
                ->color('success')
                ->modalHeading('Generate Monthly Payouts')
                ->modalDescription('Select a month to generate payouts for all teachers based on attendance records.')
                ->modalWidth('4xl')
                ->form([
                    Select::make('month')
                        ->label('Select Month')
                        ->options(function () {
                            $months = [];
                            $current = now()->startOfMonth();
                            for ($i = -6; $i <= 6; $i++) {
                                $date = $current->copy()->addMonths($i);
                                $months[$date->format('Y-m')] = $date->format('F Y');
                            }

                            return $months;
                        })
                        ->default(now()->format('Y-m'))
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set) {
                            if ($state) {
                                $service = app(PayoutCalculationService::class);
                                $summary = $service->getMonthlyPayoutSummary($state);
                                $set('summary', $summary);
                            }
                        }),
                ])
                ->modalFooterActionsAlignment(Alignment::Between)
                ->action(function (array $data) {
                    $service = app(PayoutCalculationService::class);
                    $results = $service->generatePayoutsForMonth($data['month']);

                    if (! empty($results['errors'])) {
                        Notification::make()
                            ->title('Payout Generation Completed with Errors')
                            ->body("Generated: {$results['generated']}, Updated: {$results['updated']}, Skipped: {$results['skipped']}, Errors: ".count($results['errors']))
                            ->warning()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Payouts Generated Successfully!')
                            ->body("Generated: {$results['generated']} new payouts, Updated: {$results['updated']} existing payouts, Skipped: {$results['skipped']} paid payouts")
                            ->success()
                            ->send();
                    }
                })
                ->modalSubmitActionLabel('Generate Payouts')
                ->modalCancelActionLabel('Cancel'),
            CreateAction::make(),
        ];
    }
}
