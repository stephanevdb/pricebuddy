<?php

namespace App\Services\Helpers;

use App\Enums\NotificationMethods;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationsHelper
{
    public static ?array $userSettings = null;

    public static function getServices(): Collection
    {
        return collect(SettingsHelper::getSetting('notification_services', []))
            ->mapWithKeys(fn ($service, $key) => [
                // Merge the service user settings with app values like channel.
                $key => array_merge(
                    $service,
                    [
                        'channel' => NotificationMethods::tryFrom($key)->getChannel(),
                    ]
                ),
            ]);
    }

    public static function getUserServices(User $user): Collection
    {
        return collect(($user->settings['notifications'] ?? []));
    }

    public static function getEnabled(): Collection
    {
        return self::getServices()
            ->filter(fn ($service, $serviceName) => self::isEnabled($serviceName));
    }

    public static function getUserEnabled(User $user, string $service): bool
    {
        return NotificationMethods::tryFrom($service)->requiresUserSettings()
            ? data_get(self::getUserServices($user)->toArray(), $service.'.enabled', false)
            : true;
    }

    public static function isEnabled(string $service): bool
    {
        return self::getSetting($service, 'enabled', false);
    }

    public static function getEnabledChannels(User $user): Collection
    {
        return self::getEnabled()
            ->filter(fn ($service, $serviceName) => self::getUserEnabled($user, $serviceName))
            ->pluck('channel');
    }

    public static function getSettings(string $service): array
    {
        return self::getServices()
            ->get($service) ?? [];
    }

    public static function getSetting(string $service, string $name, mixed $default = null): mixed
    {
        return data_get(self::getSettings($service), $name, $default);
    }
}
