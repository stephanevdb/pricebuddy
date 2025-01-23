<?php

namespace App\Services;

use App\Settings\AppSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Exceptions\MissingSettings;

class SettingsHelper
{
    public static ?array $settings = null;

    public static function getSettings(): array
    {
        return static::$settings ??
            (Schema::hasTable('settings')
                ? AppSettings::new()->toArray()
                : []
            );
    }

    public static function getSetting(string $name, $default = null)
    {
        try {
            return data_get(static::getSettings(), $name);
        } catch (MissingSettings $e) {
            Log::error("Attempted to get missing setting: $name, you need to run the migrations");

            return $default;
        }
    }
}
