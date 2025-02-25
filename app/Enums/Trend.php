<?php

namespace App\Enums;

use Filament\Support\Colors\ColorManager;

enum Trend: string
{
    case Up = 'up';
    case Down = 'down';
    case None = 'none';

    public static function getIcon(?string $trend): string
    {
        return match ($trend) {
            self::Down->value => Icons::TrendDown->value,
            self::Up->value => Icons::TrendUp->value,
            default => Icons::TrendNone->value,
        };
    }

    public static function getText(?string $trend): string
    {
        return match ($trend) {
            self::Down->value => __('Price decrease'),
            self::Up->value => __('Price increase'),
            default => __('No change'),
        };
    }

    public static function getColor(?string $trend): string
    {
        return match ($trend) {
            self::Down->value => 'success',
            self::Up->value => 'danger',
            default => 'gray',
        };
    }

    public static function getColorRgb(?string $trend): array
    {
        $manager = resolve(ColorManager::class);
        $colorString = self::getColor($trend);

        return $manager->getColors()[$colorString];
    }

    /**
     * Get the trend direction based on the given values. Must not be assoc array.
     * Assumes
     */
    public static function getTrendDirection(array $values): string
    {
        if (count($values) === 2 && $values[0] !== $values[1]) {
            return ($values[0] - $values[1]) > 0 ? self::Up->value : self::Down->value;
        }

        return self::None->value;
    }
}
