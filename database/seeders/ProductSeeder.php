<?php

namespace Database\Seeders;

use App\Models\Url;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Sleep;

class ProductSeeder extends Seeder
{
    /**
     * If item is array then it will be treated as a list of URLs for the same product.
     */
    protected array $urls = [
        [
            'https://api.bws.com.au/apis/ui/Product/971412',
            'https://www.liquorland.com.au/api/products/ll/vic/spirits/2614025',
            'https://www.danmurphys.com.au/product/DM_971412/sailor-jerry-the-original-spiced-rum-1l',
        ],
        //        'https://api.bws.com.au/apis/ui/Product/907623',
        //        'https://www.amazon.com.au/Patriot-Viper-Steel-DDR4-3600MHz/dp/B08N688HCH/ref=pd_ci_mcx_pspc_dp_d_2_i_1?pd_rd_w=AWO9k&content-id=amzn1.sym.c9d8026d-7c0e-4380-bf9e-ee6dc4550161&pf_rd_p=c9d8026d-7c0e-4380-bf9e-ee6dc4550161&pf_rd_r=8RMX785PNCSMKPNT7PNZ&pd_rd_wg=7AZSE&pd_rd_r=32a4235c-8e81-431b-a22b-15e7f2e72b3c&pd_rd_i=B08N688HCH&th=1',
        //        'https://www.amazon.com.au/DJI-Mini-RC-Lightweight-Intelligent/dp/B0BL3T49JF/ref=mp_s_a_1_3?crid=39ZXJEG4CMKTI&dib=eyJ2IjoiMSJ9.eddax2xvF7uWbeqpQXhFrFnTWkeGWYkHTxM59e3OkqB_-wzSGIzCa5KnzZPto_ISCtdIKNngPOSbgffP2UggVAkyNQmXJsU498lYxxV9M0TVE6LaNy88PG3oncOUJ1cj7eilfpZ7YEdQFxODBbwtoigQ4XQr0SwLY9-nnWtT7Si60lUswOOEI0SflTFjDEwxjDJYnRejy17b-Dd8RbaYQg.AE5LEbbSuYpIer_QNpr32pxtqnyRorRLHSLJrNonxdY&dib_tag=se&keywords=dji%2Bmini%2B3%2Bfly%2Bmore%2Bcombo&qid=1736578748&sprefix=dji%2Bmini%2Bfl%2Caps%2C311&sr=8-3&th=1&psc=1',
        //        'https://www.thegoodguys.com.au/dji-mini-4k-fly-more-combo-6333933',
        //        'https://www.thegoodguys.com.au/dji-neo-fly-more-combo-6292316',
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
