<?php

namespace App\Filament\Schemas\Components;

use Filament\Forms\Components\Select;

class TimeSelect extends Select
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->options(static::getTimeOptions());
    }

    protected static function getTimeOptions(): array
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
