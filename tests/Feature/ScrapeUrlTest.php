<?php

namespace Tests\Feature;

use App\Services\ScrapeUrl;
use Illuminate\Support\Uri;
use Tests\TestCase;

class ScrapeUrlTest extends TestCase
{
    public function test_scrape_output(): void
    {
        $url = 'https://www.amazon.com.au/Corsair-Vengeance-3200MHz-Aluminum-Spreader/dp/B07Y4ZZ7LQ/?_encoding=UTF8&pd_rd_w=OwAng&content-id=amzn1.sym.cfd84e9f-4c92-49ee-92a2-e66d312e9941%3Aamzn1.symc.a9cb614c-616d-4684-840d-556cb89e228d&pf_rd_p=cfd84e9f-4c92-49ee-92a2-e66d312e9941&pf_rd_r=FS3SFG5EA68MHZTB5X1P&pd_rd_wg=nt1GT&pd_rd_r=01dc87ae-2e5f-4793-b159-81a8b6538df8&ref_=pd_hp_d_atf_ci_mcx_mr_ca_hp_atf_d&th=1';

        $url = Uri::of($url)->replaceQuery([])->value();

        dump($url);

        $out = ScrapeUrl::new()->scrape($url);

        dump($out);

        $this->assertIsArray($out);
    }
}
