<?php

namespace App\Filament\Pages;

use App\Enums\Icons;
use App\Enums\NotificationMethods;
use App\Filament\Traits\FormHelperTrait;
use App\Settings\AppSettings;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\HtmlString;

class AppSettingsPage extends SettingsPage
{
    use FormHelperTrait;

    const NOTIFICATION_SERVICES_KEY = 'notification_services';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $title = 'Settings';

    protected static ?string $navigationGroup = 'System';

    protected static string $settings = AppSettings::class;

    protected static ?int $navigationSort = 100;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Scrape Settings')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TimePicker::make('scrape_schedule_time')
                            ->label('Fetch schedule time')
                            ->seconds(false)
                            ->hintIcon(Icons::Help->value, 'The time of day to get product prices')
                            ->required(),
                        TextInput::make('scrape_cache_ttl')
                            ->label('Scrape cache ttl')
                            ->hintIcon(Icons::Help->value, 'After a page is scraped, how many minutes will be the page html be cached for')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        TextInput::make('sleep_seconds_between_scrape')
                            ->label('Seconds to wait before fetching next page')
                            ->hintIcon(Icons::Help->value, 'It is recommended to wait a few seconds between fetching pages to prevent being blocked')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        TextInput::make('max_attempts_to_scrape')
                            ->label('Max scrape attempts')
                            ->hintIcon(Icons::Help->value, 'How many times to attempt to scrape a page before giving up')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                    ]),

                Section::make('Logging')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('log_retention_days')
                            ->label('Log retention days')
                            ->options([
                                7 => '7 days',
                                14 => '14 days',
                                30 => '30 days',
                                90 => '90 days',
                                180 => '180 days',
                                365 => '365 days',
                            ])
                            ->hintIcon(Icons::Help->value, 'How many days to keep logs for')
                            ->required(),
                    ]),

                self::makeFormHeading('Notifications'),

                $this->getEmailSettings(),
                $this->getPushoverSettings(),
            ]);
    }

    protected function getEmailSettings(): Section
    {
        return self::makeSettingsSection(
            'Email',
            self::NOTIFICATION_SERVICES_KEY,
            NotificationMethods::Mail->value,
            [
                TextInput::make('smtp_host')
                    ->label('SMTP host')
                    ->hintIcon(Icons::Help->value, 'Host domain or IP address of the SMTP server')
                    ->required(),
                TextInput::make('smtp_port')
                    ->label('SMTP Port')
                    ->hintIcon(Icons::Help->value, 'The port of the SMTP server')
                    ->required()
                    ->default('25'),
                TextInput::make('smtp_user')
                    ->label('SMTP Username')
                    ->hintIcon(Icons::Help->value, 'The optional username for the SMTP server'),
                TextInput::make('smtp_password')
                    ->password()
                    ->label('SMTP Password')
                    ->hintIcon(Icons::Help->value, 'The optional password for the SMTP server'),
            ]
        );
    }

    protected function getPushoverSettings(): Section
    {
        return self::makeSettingsSection(
            'Pushover',
            self::NOTIFICATION_SERVICES_KEY,
            NotificationMethods::Pushover->value,
            [
                TextInput::make('token')
                    ->label('Pushover token')
                    ->hint(new HtmlString('<a href="https://pushover.net/apps/build" target="_blank">Create an application</a>'))
                    ->required(),
            ]
        );
    }
}
