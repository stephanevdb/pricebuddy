<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\Url;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class PriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price' => 10.00,
            'url_id' => Url::factory(),
            'store_id' => Store::factory(),
        ];
    }
}
