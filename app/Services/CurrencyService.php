<?php

namespace App\Services;

use NumberFormatter;

class CurrencyService
{
    public static function getSymbol(?string $locale = null): string
    {
        $locale = $locale ?? config('app.faker_locale', 'en');

        return (new NumberFormatter($locale, NumberFormatter::CURRENCY))
            ->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
    }

    public static function toFloat(mixed $value): float
    {
        if (is_float($value)) {
            return $value;
        } elseif (is_int($value)) {
            return (float) $value;
        } elseif (is_string($value)) {
            return floatval(preg_replace('/[^\d\.]/', '', $value));
        } else {
            return 0.0;
        }
    }
}
