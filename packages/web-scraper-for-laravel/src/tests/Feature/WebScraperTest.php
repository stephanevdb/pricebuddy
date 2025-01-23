<?php

namespace Jez500\WebScraperForLaravel\tests\Feature;

use Illuminate\Support\Collection;
use Jez500\WebScraperForLaravel\Facades\WebScraper;
use Jez500\WebScraperForLaravel\WebScraperHttp;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class WebScraperTest extends TestCase
{
    public function test_can_instantiate()
    {
        $scraper = WebScraper::http();
        $this->assertInstanceOf(WebScraperHttp::class, $scraper);
    }

    public function test_can_set_url()
    {
        $scraper = WebScraper::http();
        $scraper->setUrl('https://example.com');
        $this->assertEquals('https://example.com', $scraper->getUrl());
    }

    public function test_can_set_use_cache()
    {
        $scraper = WebScraper::http();
        $scraper->setUseCache(false);
        $this->assertFalse($scraper->getUseCache());
    }

    public function test_can_set_cache_ttl()
    {
        $scraper = WebScraper::http();
        $scraper->setCacheMinsTtl(60);
        $this->assertEquals(60, $scraper->getCacheMinsTtl());
    }

    public function test_can_build_headers()
    {
        $scraper = WebScraper::http();
        $headers = $scraper->buildHeaders();
        $this->assertArrayHasKey('User-Agent', $headers);
        $this->assertArrayHasKey('Accept', $headers);
        $this->assertArrayHasKey('Accept-Language', $headers);
        $this->assertArrayHasKey('Accept-Encoding', $headers);
    }

    public function test_can_get_dom()
    {
        $crawler = WebScraper::http()->from('https://example.com/')->get()->getDom();
        $this->assertInstanceOf(Crawler::class, $crawler);
    }

    public function test_can_get_selector()
    {
        $result = WebScraper::http()->from('https://example.com/')->get()->getSelector('title');
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame('Example Domain', $result->first());
    }

    public function test_can_get_selector_via_callback()
    {
        $result = WebScraper::http()->from('https://example.com/')
            ->get()
            ->getSelector('title', fn (Crawler $node) => $node->text());

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame('Example Domain', $result->first());
    }

    public function test_can_get_regex()
    {
        $result = WebScraper::http()->from('https://example.com/')
            ->get()
            ->getRegex('~<title>(.*)</title>~');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame('Example Domain', $result->first());
    }
}
