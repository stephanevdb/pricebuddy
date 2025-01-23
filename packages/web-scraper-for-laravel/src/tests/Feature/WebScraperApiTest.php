<?php

namespace Jez500\WebScraperForLaravel\tests\Feature;

use Illuminate\Support\Collection;
use Jez500\WebScraperForLaravel\WebScraperApi;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class WebScraperApiTest extends TestCase
{
    //    public function test_can_instantiate()
    //    {
    //        $scraper = new WebScraper;
    //        $this->assertInstanceOf(WebScraper::class, $scraper);
    //    }
    //
    //    public function test_can_set_url()
    //    {
    //        $scraper = new WebScraper;
    //        $scraper->setUrl('https://example.com');
    //        $this->assertEquals('https://example.com', $scraper->getUrl());
    //    }
    //
    //    public function test_can_set_use_cache()
    //    {
    //        $scraper = new WebScraper;
    //        $scraper->setUseCache(false);
    //        $this->assertFalse($scraper->getUseCache());
    //    }
    //
    //    public function test_can_set_cache_ttl()
    //    {
    //        $scraper = new WebScraper;
    //        $scraper->setCacheMinsTtl(60);
    //        $this->assertEquals(60, $scraper->getCacheMinsTtl());
    //    }
    //
    //    public function test_can_build_headers()
    //    {
    //        $scraper = new WebScraper;
    //        $headers = $scraper->buildHeaders();
    //        $this->assertArrayHasKey('User-Agent', $headers);
    //        $this->assertArrayHasKey('Accept', $headers);
    //        $this->assertArrayHasKey('Accept-Language', $headers);
    //        $this->assertArrayHasKey('Accept-Encoding', $headers);
    //    }

    public function test_can_get_dom()
    {
        $crawler = WebScraperApi::from('https://www.danmurphys.com.au/product/DM_91192/bacardi-carta-blanca-rum-700ml')
            ->get()
            ->getSelector('.pack__price');

        dd($crawler);

        $this->assertInstanceOf(Crawler::class, $crawler);
    }
    //
    //    public function test_can_get_selector()
    //    {
    //        $result = WebScraper::from('https://example.com/')->get()->getSelector('title');
    //        $this->assertInstanceOf(Collection::class, $result);
    //        $this->assertSame('Example Domain', $result->first());
    //    }
    //
    //    public function test_can_get_selector_via_callback()
    //    {
    //        $result = WebScraper::from('https://example.com/')
    //            ->get()
    //            ->getSelector('title', fn (Crawler $node) => $node->text());
    //
    //        $this->assertInstanceOf(Collection::class, $result);
    //        $this->assertSame('Example Domain', $result->first());
    //    }
    //
    //    public function test_can_get_regex()
    //    {
    //        $result = WebScraper::from('https://example.com/')
    //            ->get()
    //            ->getRegex('~<title>(.*)</title>~');
    //
    //        $this->assertInstanceOf(Collection::class, $result);
    //        $this->assertSame('Example Domain', $result->first());
    //    }
}
