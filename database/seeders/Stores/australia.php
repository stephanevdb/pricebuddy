<?php

use App\Enums\ScraperService;

return [
    // Retail.
    [
        'name' => 'Amazon AU',
        'slug' => 'amazon-au',
        'domains' => [
            ['domain' => 'amzn.asia'],
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
    ],
    [
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
    ],
    [
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
    ],
    [
        'name' => 'eBay AU',
        'slug' => 'ebay-au',
        'domains' => [
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
    ],
    [
        'name' => 'K-Mart AU',
        'initials' => 'KM',
        'slug' => 'kmart-au',
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
    ],

    // Liquor.
    [
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
    ],
    [
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
    ],
    [
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
    ],

];
