<?php

namespace App\Enums;

enum DayOfWeek: int
{
    case Monday = 1;
    case Tuesday = 2;
    case Wednesday = 3;
    case Thursday = 4;
    case Friday = 5;
    case Saturday = 6;
    case Sunday = 7;

    public function label(): string
    {
        return match ($this) {
            self::Monday => 'Monday',
            self::Tuesday => 'Tuesday',
            self::Wednesday => 'Wednesday',
            self::Thursday => 'Thursday',
            self::Friday => 'Friday',
            self::Saturday => 'Saturday',
            self::Sunday => 'Sunday',
        };
    }

    public static function options(): array
    {
        return [
            self::Monday->value => self::Monday->label(),
            self::Tuesday->value => self::Tuesday->label(),
            self::Wednesday->value => self::Wednesday->label(),
            self::Thursday->value => self::Thursday->label(),
            self::Friday->value => self::Friday->label(),
            self::Saturday->value => self::Saturday->label(),
            self::Sunday->value => self::Sunday->label(),
        ];
    }

    public static function fromValue(int $value): ?self
    {
        return match ($value) {
            1 => self::Monday,
            2 => self::Tuesday,
            3 => self::Wednesday,
            4 => self::Thursday,
            5 => self::Friday,
            6 => self::Saturday,
            7 => self::Sunday,
            default => null,
        };
    }
}
