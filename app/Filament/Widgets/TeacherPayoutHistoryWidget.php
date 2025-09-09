<?php

namespace App\Filament\Widgets;

use App\Models\TeacherPayout;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class TeacherPayoutHistoryWidget extends TableWidget
{
    protected static ?int $sort = 3; // Third widget for teachers
    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return 'My Payout History';
    }

    public static function canView(): bool
    {
        return Auth::user()?->role?->name === 'Teacher';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TeacherPayout::query()
                    ->where('teacher_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->limit(8)
            )
            ->columns([
                TextColumn::make('month')
                    ->label('Month')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::createFromFormat('Y-m', $state)->format('F Y'))
                    ->sortable(),

                TextColumn::make('total_pay')
                    ->label('Amount')
                    ->money('USD')
                    ->color('success'),

                IconColumn::make('is_paid')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                TextColumn::make('paid_at')
                    ->label('Paid Date')
                    ->dateTime()
                    ->placeholder('Pending')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Generated')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
