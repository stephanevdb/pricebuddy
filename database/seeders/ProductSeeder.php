<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Tag;
use App\Models\Url;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Sleep;

class ProductSeeder extends Seeder
{
    /**
     * Dummy products to create. No scraping.
     */
    protected array $dummy = [
        [
            'title' => 'Amazon Echo Dot',
            'urls' => [
                'https://www.amazon.com/Amazon-vibrant-helpful-routines-Charcoal/dp/B09B8V1LZ3' => ['49.99', '49.99', '39.99', '39.99', '59', '59', '49.99'],
                'https://www.ebay.com/itm/255775080771' => ['41.99', '41.99', '35.99', '19.99', '19.99', '49.99', '49.99'],
            ],
            'image' => 'https://m.media-amazon.com/images/I/71yRY8YlAbL._AC_SX679_.jpg',
            'tag' => 'Tech',
        ],
        [
            'title' => 'Scott paper towels',
            'urls' => [
                'https://www.amazon.com/Scott-Towels-Choose-Sheet-Regular/dp/B0CT67D4JH/ref=sr_1_3' => ['19', '19', '29', '29', '35'],
                'https://www.ebay.com/itm/365354442418' => ['39', '39', '29', '29', '29'],
            ],
            'image' => 'https://m.media-amazon.com/images/I/812-y3MIhmL._AC_SX679_PIbundle-2,TopRight,0,0_SH20_.jpg',
            'tag' => 'Household',
        ],
        [
            'title' => 'Airpods 4',
            'urls' => [
                'https://www.amazon.com/Apple-Bluetooth-Headphones-Personalized-Effortless/dp/B0DGHMNQ5Z' => ['199', '199', '189', '189', '199'],
                'https://www.ebay.com/itm/405476505625' => ['129', '129', '129', '129', '119'],
            ],
            'image' => 'https://m.media-amazon.com/images/I/61iBtxCUabL._AC_SX679_.jpg',
            'tag' => 'Tech',
        ],
    ];

    /**
     * Real urls to create with scraping.
     *
     * If item is array then it will be treated as a list of URLs for the same product.
     */
    protected array $urls = [
        [
            'https://api.bws.com.au/apis/ui/Product/971412',
            'https://www.liquorland.com.au/api/products/ll/vic/spirits/2614025',
            'https://www.danmurphys.com.au/product/DM_971412/sailor-jerry-the-original-spiced-rum-1l',
        ],
        'https://www.thegoodguys.com.au/dji-neo-fly-more-combo-6292316',
        [
            'https://www.amazon.com.au/DJI-QuickShots-Stabilized-Propeller-Controller-Free/dp/B07FTPX71F?th=1',
            'https://www.thegoodguys.com.au/dji-neo-6292315',
            'https://www.jbhifi.com.au/products/dji-neo-drone',
            'https://www.ebay.com.au/itm/405209468795',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->runDummy();
    }

    public function runDummy(): void
    {
        $userId = User::oldest('id')->first()?->id ?? User::factory()->create()->id;

        foreach ($this->dummy as $productData) {
            $factory = Product::factory();

            foreach ($productData['urls'] as $url => $prices) {
                $factory = $factory->addUrlWithPrices($url, $prices);
            }

            /** @var Product $product */
            $product = $factory->createOne([
                'title' => $productData['title'],
                'image' => $productData['image'],
                'user_id' => $userId,
            ]);

            $product->updatePriceCache();

            if ($tag = Tag::where('name', $productData['tag'])->first()) {
                $product->tags()->sync([$tag->id]);
                $product->save();
            }
        }
    }

    public function runReal(): void
    {
        $productModel = null;
        $userId = User::oldest('id')->first()?->id ?? User::factory()->create()->id;

        foreach ($this->urls as $urlList) {
            $urls = Arr::wrap($urlList);
            foreach ($urls as $idx => $url) {
                $productId = $idx > 0 ? $productModel?->id : null;

                $urlModel = Url::createFromUrl($url, $productId, $userId);

                if (! $urlModel) {
                    dump('Failed to scrape URL: '.$url);

                    continue;
                }

                $productModel = $urlModel->product;

                $this->createRandomPriceHistory($urlModel);

                $productModel->updatePriceCache();

                // Try to avoid getting blocked.
                Sleep::for(10)->seconds();
            }
        }
    }

    /**
     * Create random price history for the given URL.
     */
    protected function createRandomPriceHistory(Url $urlModel): void
    {
        $priceModel = $urlModel->prices()->first();

        if (empty($priceModel)) {
            dump('Url missing price, using $100: '.$urlModel->url);
            $price = 100;
        } else {
            $price = $priceModel->price;
        }

        // Set last 10 days of prices.
        for ($i = 1; $i <= 10; $i++) {
            $randOffset = rand(-20, 10);
            $randPrice = $price + $randOffset;
            $randPrice = $randPrice <= 0 ? 2.0 : $randPrice;
            $fakePriceModel = $urlModel->updatePrice($randPrice);
            $fakePriceModel->created_at = now()->subDays($i)->toDateTimeString();
            $fakePriceModel->updated_at = $fakePriceModel->created_at;
            $fakePriceModel->save();
        }
    }
}
