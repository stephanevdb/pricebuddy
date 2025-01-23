<?php

namespace Database\Factories;

use App\Enums\ScraperService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'initials' => '',
            'domains' => [],
            'scrape_strategy' => [],
            'settings' => [
                'scraper_service' => ScraperService::Http->value,
                'scraper_service_settings' => '',
            ],
        ];
    }
}
