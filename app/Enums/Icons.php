<?php

namespace App\Enums;

enum Icons: string
{
    case Help = 'heroicon-o-information-circle';
    case TrendUp = 'heroicon-m-arrow-trending-up';

    case TrendDown = 'heroicon-m-arrow-trending-down';

    case TrendNone = 'heroicon-m-arrow-long-right';

    public static function getTrendIcon(?string $trend): string
    {
        return match ($trend) {
            'down' => Icons::TrendDown->value,
            'up' => Icons::TrendUp->value,
            default => Icons::TrendNone->value,
        };
    }
}
