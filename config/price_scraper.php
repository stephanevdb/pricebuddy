<?php

return [
    'sleep_seconds_between_scrape' => 10,
    'chunk_size' => 10,
    'scrape_cache_ttl_mins' => 720,
    'schedule_time' => '06:00',
    'scraper_api_url' => env('SCRAPER_BASE_URL', 'http://scraper:3000'),
];
