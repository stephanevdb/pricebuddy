<?php

use App\Console\Commands\ScraperFetchAll;
use App\Services\Helpers\SettingsHelper;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        // When to check for new prices
        $schedule->command(ScraperFetchAll::COMMAND, ['--log'])
            ->dailyAt(SettingsHelper::getSetting('scrape_schedule_time', '06:00'));
    })
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
