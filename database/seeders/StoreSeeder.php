<?php

namespace Database\Seeders;

use App\Enums\ScraperService;
use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Store::factory()->createOne([
            'name' => 'Amazon',
            'domains' => [
                ['domain' => 'amazon.com'],
                ['domain' => 'www.amazon.com'],
                ['domain' => 'amazon.com.au'],
                ['domain' => 'www.amazon.com.au'],
            ],
            'scrape_strategy' => [
                'title' => [
                    'value' => 'title',
                    'type' => 'selector',
                ],
                'price' => [
                    'value' => '.a-price > .a-offscreen',
                    'type' => 'selector',
                ],
                'image' => [
                    'value' => '~"hiRes":"(.+?)"~',
                    'type' => 'regex',
                ],
            ],
            'user_id' => 1,
        ]);

        Store::factory()->createOne([
            'name' => 'BWS',
            'domains' => [
                ['domain' => 'api.bws.com.au'],
            ],
            'scrape_strategy' => [
                'title' => [
                    'value' => 'Products.0.Name',
                    'type' => 'json',
                ],
                'price' => [
                    'value' => 'Products.0.Price',
                    'type' => 'json',
                ],
                'image' => [
                    'value' => 'PackDefaultStockCode',
                    'type' => 'json',
                    'prepend' => 'https://edgmedia.bws.com.au/bws/media/products/',
                    'append' => '-1.png',
                ],
            ],
            'user_id' => 1,
        ]);

        Store::factory()->createOne([
            'name' => 'Liquorland',
            'initials' => 'LL',
            'domains' => [
                ['domain' => 'www.liquorland.com.au'],
            ],
            'scrape_strategy' => [
                'title' => [
                    'value' => 'product.name',
                    'type' => 'json',
                ],
                'price' => [
                    'value' => 'product.price.current',
                    'type' => 'json',
                ],
                'image' => [
                    'value' => 'product.image.thumbnailImage-missing',
                    'type' => 'json',
                ],
            ],
            'user_id' => 1,
        ]);

        Store::factory()->createOne([
            'name' => 'Dan Murphys',
            'initials' => 'LL',
            'domains' => [
                ['domain' => 'www.danmurphys.com.au', 'danmurphys.com.au'],
            ],
            'scrape_strategy' => [
                'title' => [
                    'value' => 'meta[property=og:title]|content',
                    'type' => 'selector',
                ],
                'price' => [
                    'value' => '.pack__price-info',
                    'type' => 'selector',
                ],
                'image' => [
                    'value' => 'meta[property=og:image]|content',
                    'type' => 'selector',
                ],
            ],
            'user_id' => 1,
            'settings' => [
                'scraper_service' => ScraperService::Api->value,
                'scraper_service_settings' => '',
            ],
        ]);

        Store::factory()->createOne([
            'name' => 'Good Guys',
            'domains' => [
                ['domain' => 'thegoodguys.com.au'],
                ['domain' => 'www.thegoodguys.com.au'],
            ],
            'scrape_strategy' => [
                'title' => [
                    'value' => 'meta[property=og:title]|content',
                    'type' => 'selector',
                ],
                'price' => [
                    'value' => 'meta[property=og:price:amount]|content',
                    'type' => 'selector',
                ],
                'image' => [
                    'value' => 'meta[property=og:image]|content',
                    'type' => 'selector',
                ],
            ],
            'user_id' => 1,
        ]);

        Store::factory()->createOne([
            'name' => 'JB Hi-Fi',
            'initials' => 'JB',
            'domains' => [
                ['domain' => 'jbhifi.com.au'],
                ['domain' => 'www.jbhifi.com.au'],
            ],
            'scrape_strategy' => [
                'title' => [
                    'value' => 'meta[property=og:title]|content',
                    'type' => 'selector',
                ],
                'price' => [
                    'value' => 'meta[property=og:price:amount]|content',
                    'type' => 'selector',
                ],
                'image' => [
                    'value' => 'meta[property=og:image]|content',
                    'type' => 'selector',
                ],
            ],
            'user_id' => 1,
        ]);

        Store::factory()->createOne([
            'name' => 'eBay',
            'domains' => [
                ['domain' => 'ebay.com'],
                ['domain' => 'www.ebay.com'],
                ['domain' => 'ebay.com.au'],
                ['domain' => 'www.ebay.com.au'],
            ],
            'scrape_strategy' => [
                'title' => [
                    'value' => 'meta[property=og:title]|content',
                    'type' => 'selector',
                ],
                'price' => [
                    'value' => '.x-price-primary',
                    'type' => 'selector',
                ],
                'image' => [
                    'value' => 'meta[property=og:image]|content',
                    'type' => 'selector',
                ],
            ],
            'user_id' => 1,
        ]);

        Store::factory()->createOne([
            'name' => 'K-Mart',
            'initials' => 'KM',
            'domains' => [
                ['domain' => 'kmart.com.au'],
                ['domain' => 'www.kmart.com.au'],
            ],
            'scrape_strategy' => [
                'title' => [
                    'value' => 'title',
                    'type' => 'selector',
                ],
                'price' => [
                    'value' => '.product-price-large',
                    'type' => 'selector',
                ],
                'image' => [
                    'value' => '~"image":"(http.*\.jpg)~',
                    'type' => 'regex',
                ],
            ],
            'user_id' => 1,
        ]);
    }
}
