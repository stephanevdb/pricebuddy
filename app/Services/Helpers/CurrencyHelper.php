<?php

namespace App\Services\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use NumberFormatter;

/**
 * Helpers to make dealing with currencies easier.
 */
class CurrencyHelper
{
    public static function getLocale(): string
    {
        return config('app.currency_locale', 'en_US');
    }

    public static function getCurrency(): string
    {
        return self::getCurrencyDetail()['iso'] ?? 'USD';
    }

    public static function getCurrencyDetail(): ?array
    {
        return once(fn () => self::getAllCurrencies()
            ->firstWhere('locale', self::getLocale())
        );
    }

    public static function getAllCurrencies(): Collection
    {
        return collect(json_decode(
            file_get_contents(base_path('/resources/datasets/currency.json')), true)
        );
    }

    public static function getSymbol(?string $locale = null): string
    {
        return (new NumberFormatter($locale ?? self::getLocale(), NumberFormatter::CURRENCY))
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

    public static function toString(mixed $value, int $maxPrecision = 2, ?string $locale = null): string
    {
        return Number::currency(
            round(self::toFloat($value), $maxPrecision),
            locale: ($locale ?? self::getLocale())
        );
    }
}
