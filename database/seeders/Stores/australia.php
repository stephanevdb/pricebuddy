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
    [
        'name' => 'Officeworks',
        'initials' => 'OW',
        'slug' => 'officeworks-au',
        'domains' => [
            ['domain' => 'officeworks.com.au'],
            ['domain' => 'www.officeworks.com.au'],
            ['domain' => 'api.officeworks.com.au'],
        ],
        'scrape_strategy' => [
            'title' => [
                'value' => 'title',
                'type' => 'selector',
            ],
            'price' => [
                'value' => '~\"edlpPrice\":\"(.*?)\"~',
                'type' => 'regex',
            ],
            'image' => [
                'value' => '~\"image\":\"(.*?)\"~',
                'type' => 'regex',
            ],
        ],
        'user_id' => 1,
    ],
    [
        'name' => 'Target AU',
        'initials' => 'TA',
        'slug' => 'target-au',
        'domains' => [
            ['domain' => 'target.com.au'],
            ['domain' => 'www.target.com.au'],
        ],
        'scrape_strategy' => [
            'title' => [
                'value' => 'meta[property=og:title]|content',
                'type' => 'selector',
            ],
            'price' => [
                'value' => '~\"valueString\"\:\"(.*?)\"~',
                'type' => 'regex',
            ],
            'image' => [
                'value' => 'meta[property=og:image]|content',
                'type' => 'selector',
            ],
        ],
        'user_id' => 1,
    ],
    [
        'name' => 'Bunnings',
        'initials' => 'BU',
        'slug' => 'bunnings-au',
        'domains' => [
            ['domain' => 'bunnings.com.au'],
            ['domain' => 'www.bunnings.com.au'],
        ],
        'scrape_strategy' => [
            'title' => [
                'value' => 'meta[property=og:title]|content',
                'type' => 'selector',
            ],
            'price' => [
                'value' => '[data-locator="product-price"]',
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
        'name' => 'Big W',
        'initials' => 'BW',
        'slug' => 'big-w-au',
        'domains' => [
            ['domain' => 'bigw.com.au'],
            ['domain' => 'www.bigw.com.au'],
        ],
        'scrape_strategy' => [
            'title' => [
                'value' => 'meta[property=og:title]|content',
                'type' => 'selector',
            ],
            'price' => [
                'value' => '[data-testid="price-value"]',
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
        'name' => 'Coles AU',
        'initials' => 'CO',
        'slug' => 'coles-au',
        'domains' => [
            ['domain' => 'coles.com.au'],
            ['domain' => 'www.coles.com.au'],
        ],
        'scrape_strategy' => [
            'title' => [
                'value' => 'meta[property=og:title]|content',
                'type' => 'selector',
            ],
            'price' => [
                'value' => '.price__value',
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
        'name' => 'Woolworths AU',
        'initials' => 'WW',
        'domains' => [
            ['domain' => 'woolworths.com.au'],
            ['domain' => 'www.woolworths.com.au'],
        ],
        'scrape_strategy' => [
            'title' => [
                'value' => 'meta[property=og:title]|content',
                'type' => 'selector',
            ],
            'price' => [
                'value' => '.price-parts',
                'type' => 'selector',
            ],
            'image' => [
                'value' => 'meta[property=og:image]|content',
                'type' => 'selector',
            ],
        ],
        'settings' => [
            'scraper_service' => ScraperService::Api->value,
            'scraper_service_settings' => '',
        ],
    ],

    // Liquor.
    [
        'name' => 'BWS API',
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
        'name' => 'BWS',
        'domains' => [
            ['domain' => 'bws.com.au'],
            ['domain' => 'www.bws.com.au'],
        ],
        'scrape_strategy' => [
            'title' => [
                'value' => '.detail-item_brand',
                'type' => 'selector',
            ],
            'price' => [
                'value' => '.trolley-controls_volume_price--dollars',
                'type' => 'selector',
            ],
            'image' => [
                'value' => '.product-image',
                'type' => 'selector',
            ],
        ],
        'settings' => [
            'scraper_service' => ScraperService::Api->value,
            'scraper_service_settings' => '',
        ],
    ],
    [
        'name' => 'Liquorland',
        'initials' => 'LL',
        'domains' => [
            ['domain' => 'liquorland.com.au'],
            ['domain' => 'www.liquorland.com.au'],
        ],
        'scrape_strategy' => [
            'title' => [
                'value' => 'meta[property=og:title]|content',
                'type' => 'selector',
            ],
            'price' => [
                'value' => '.FormattedAmount',
                'type' => 'selector',
            ],
            'image' => [
                'value' => 'meta[property=og:image]|content',
                'type' => 'selector',
            ],
        ],
        'settings' => [
            'scraper_service' => ScraperService::Api->value,
            'scraper_service_settings' => '',
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
