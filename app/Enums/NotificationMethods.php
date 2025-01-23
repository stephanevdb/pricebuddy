<?php

namespace App\Enums;

use NotificationChannels\Pushover\PushoverChannel;

enum NotificationMethods: string
{
    case Mail = 'mail';

    case Database = 'database';

    case Pushover = 'pushover';

    public function getChannel(): string
    {
        return match ($this) {
            self::Pushover => PushoverChannel::class,
            default => $this->value,
        };
    }

    public function requiresUserSettings(): bool
    {
        return match ($this) {
            self::Database => false,
            default => true,
        };
    }
}
