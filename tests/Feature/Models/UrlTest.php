<?php

namespace Tests\Feature\Models;

use App\Models\Price;
use App\Models\Product;
use App\Models\Store;
use App\Models\Url;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Number;
use Tests\TestCase;
use Tests\Traits\ScraperTrait;

class UrlTest extends TestCase
{
    use RefreshDatabase;
    use ScraperTrait;

    const TEST_URL = 'https://example.com/product';

    protected User $user;

    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::factory()->createOne([
            'domains' => [['domain' => parse_url(self::TEST_URL, PHP_URL_HOST)]],
        ]);

        $this->user = User::factory()->create();
    }

    public function test_user_is_required_to_create_url()
    {
        $this->mockScrape(10, 'test');

        $this->expectException(AuthorizationException::class);

        Url::createFromUrl(self::TEST_URL);
    }

    public function test_create_from_url_with_valid_data()
    {
        $this->actingAs($this->user);

        $scrapeData = [
            'title' => 'Example Product',
            'price' => 100,
        ];

        $this->mockScrape($scrapeData['price'], $scrapeData['title']);

        $urlModel = Url::createFromUrl(self::TEST_URL);

        $this->assertInstanceOf(Url::class, $urlModel);
        $this->assertEquals(self::TEST_URL, $urlModel->url);
        $this->assertInstanceOf(Store::class, $urlModel->store);
        $this->assertEquals($scrapeData['title'], $urlModel->product->title);
    }

    public function test_create_from_url_with_invalid_data()
    {
        $this->mockScrape('', '');

        $urlModel = Url::createFromUrl(self::TEST_URL);

        $this->assertFalse($urlModel);
    }

    public function test_update_price_with_valid_data()
    {
        $product = Product::factory()->create();
        $url = Url::factory()->createOne([
            'url' => self::TEST_URL,
            'product_id' => $product->id,
            'store_id' => $this->store->id,
        ]);

        $this->mockScrape('$100', 'foo');

        $priceModel = $url->updatePrice();

        $this->assertInstanceOf(Price::class, $priceModel);
        $this->assertEquals(100.0, $priceModel->price);
    }

    public function test_update_price_with_invalid_data()
    {
        $product = Product::factory()->create();
        $url = Url::factory()->createOne([
            'url' => self::TEST_URL,
            'product_id' => $product->id,
            'store_id' => $this->store->id,
        ]);

        $this->mockScrape('invalid', 'invalid');

        $priceModel = $url->updatePrice();

        $this->assertNull($priceModel);
    }

    public function test_product_name_short_returns_correct_value()
    {
        $product = Product::factory()->create(['title' => 'A long title that is too long, it should be trimmed to a limit so not so long']);
        $url = Url::factory()->create(['product_id' => $product->id]);

        $this->assertEquals('A long title that is...', $url->product_name_short);
    }

    public function test_store_name_returns_correct_value()
    {
        $store = Store::factory()->create(['name' => 'Example Store']);
        $url = Url::factory()->create(['store_id' => $store->id]);

        $this->assertEquals('Example Store', $url->store_name);
    }

    public function test_product_url_returns_correct_value()
    {
        $product = Product::factory()->create();
        $url = Url::factory()->create(['product_id' => $product->id]);

        $this->assertEquals($product->action_urls['view'], $url->product_url);
    }

    public function test_latest_price_formatted_returns_correct_value()
    {
        $url = Url::factory()->create();
        $price = Price::factory()->create(['url_id' => $url->id, 'price' => 100]);

        $this->assertEquals(Number::currency(100), $url->latest_price_formatted);
    }

    public function test_average_price_returns_correct_value()
    {
        $url = Url::factory()->create();
        Price::factory()->create(['url_id' => $url->id, 'price' => 100]);
        Price::factory()->create(['url_id' => $url->id, 'price' => 200]);

        $this->assertEquals(Number::currency(150), $url->average_price);
    }
}
