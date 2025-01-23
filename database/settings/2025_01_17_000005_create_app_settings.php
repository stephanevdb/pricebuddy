<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->migrator->add('app.scrape_schedule_time', '06:00');
        $this->migrator->add('app.scrape_cache_ttl', 720);
        $this->migrator->add('app.sleep_seconds_between_scrape', 10);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->migrator->delete('app.scrape_schedule_time');
        $this->migrator->delete('app.scrape_cache_ttl');
        $this->migrator->delete('app.sleep_seconds_between_scrape');
    }
};
