<?php

namespace Models;

use App\Dto\PriceCacheDto;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_build_price_cache() {}

    public function test_price_cache_dto()
    {
        $product = Product::factory()->addUrlsAndPrices(3, 3)->createOne();
        $this->assertCount(3, $product->urls);
        $this->assertCount(9, $product->prices);

        $cache = $product->refresh()->getPriceCache();
        $this->assertCount(3, $cache);

        /** @var PriceCacheDto $firstItem */
        $firstItem = $cache->first();

        $this->assertIsInt($firstItem->getStoreId());
        $this->assertIsString($firstItem->getStoreName());
        $this->assertIsInt($firstItem->getUrlId());
        $this->assertIsString($firstItem->getUrl());
        $this->assertTrue(in_array($firstItem->getTrend(), ['up', 'down', 'none']));
        $this->assertIsFloat($firstItem->getPrice());
        $this->assertInstanceOf(Collection::class, $firstItem->getHistory());
    }

    //        $product = Product::factory()->create();
    //        $productStore = ProductStore::factory()->create([
    //            'product_id' => $product->id,
    //            'price' => 100,
    //            'notify_price' => 90,
    //        ]);
    //        $currency = Currency::factory()->create();
    //        $store = Store::factory()->create([
    //            'currency_id' => $currency->id,
    //        ]);
    //        $currencies = [$currency->id => 'USD'];
    //        $stores = [$store->id => ['currency_id' => $currency->id]];
    //
    //        $result = ProductHelper::prepare_multiple_prices_in_table($product, $currencies, $stores);
    //
    //        $this->assertEquals('<p style="color:green">USD100.00</p>', $result);

}
