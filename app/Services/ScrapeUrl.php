<?php

namespace App\Services;

use App\Enums\ScraperService;
use App\Models\Store;
use App\Settings\AppSettings;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Uri;
use Jez500\WebScraperForLaravel\Exceptions\DomSelectorException;
use Jez500\WebScraperForLaravel\Facades\WebScraper;
use Jez500\WebScraperForLaravel\WebScraperApi;
use Jez500\WebScraperForLaravel\WebScraperInterface;
use Psr\Log\LoggerInterface;

class ScrapeUrl
{
    const SELECTOR_ATTR_DELIMITER = '|';

    protected WebScraperInterface $webScraper;

    protected LoggerInterface $logger;

    protected string $scraperService = 'api';

    protected array $keys = [
        'title',
        'description',
        'price',
        'image',
    ];

    public function __construct(protected string $url)
    {
        // @phpstan-ignore-next-line - withContext is valid.
        $this->logger = Log::channel('db')->withContext(['url' => $url]);
    }

    public static function new(string $url): self
    {
        return resolve(static::class, ['url' => $url]);
    }

    public function setScraper(string $scraper): self
    {
        $this->scraperService = $scraper;
        $scraper = WebScraper::make($this->scraperService);

        if ($this->scraperService === ScraperService::Api->value) {
            /** @var WebScraperApi $scraper */
            $scraper->setScraperApiBaseUrl(
                config('price_scraper.scraper_api_url', 'http://scraper:3000')
            );
        }

        $this->webScraper = $scraper;

        return $this;
    }

    public function scrape(array $options = []): array
    {
        $attempt = 0;
        $maxAttempts = 3;
        $output = [];

        while ($attempt < $maxAttempts) {
            $attempt++;
            $output = $this->scrapeUrl($options);

            if (! empty($output['title'])) {
                break;
            }
        }

        foreach (['title', 'price'] as $required) {
            if (empty($output[$required])) {
                $this->logger->error('Error scraping URL', [
                    'attempts' => $attempt,
                    'error' => 'Missing required field: '.$required,
                    'scrape_errors' => $output['errors'],
                    'scraped_html' => $output['body'],
                ]);
                $this->errorNotification('Missing required field: '.$required);

                return $output;
            }
        }

        return $output;
    }

    protected function scrapeUrl(array $options = []): array
    {
        $store = data_get($options, 'store') ?? $this->getStore();
        $useCache = data_get($options, 'use_cache', true);
        $this->setScraper($store->scraper_service);

        $scraper = $this->webScraper->from($this->url)
            ->setCacheMinsTtl(AppSettings::new()->scrape_cache_ttl)
            ->setUseCache($useCache)
            ->setOptions($store->scraper_options);

        $page = $scraper->get();

        $output = [
            'store' => $store,
        ];

        if ($errors = $scraper->getErrors()) {
            $this->logger->error('Error scraping URL', [
                'store_id' => $store?->getKey(),
                'errors' => $errors,
            ]);
            $this->errorNotification('Error scraping URL check logs');

            return $output;
        }

        $strategy = data_get($store, 'scrape_strategy', []);

        foreach ($this->keys as $key) {
            if (empty($strategy[$key]) || ! is_array($strategy[$key])) {
                $output[$key] = null;
            } else {
                $output[$key] = $this->scrapeOption($page, $strategy[$key]);
            }
        }

        $output['body'] = $page->getBody();
        $output['errors'] = $scraper->getErrors();

        return $output;
    }

    public function getStore(): ?Store
    {
        $host = Uri::of($this->url)->host();

        return Store::whereJsonContains('domains', ['domain' => $host])->first();
    }

    protected function scrapeOption(WebScraperInterface $scraper, array $options): ?string
    {
        $type = data_get($options, 'type');
        $value = data_get($options, 'value');

        $method = match ($type) {
            'regex' => 'getRegex',
            'json' => 'getJson',
            default => 'getSelector'
        };

        $value = match ($type) {
            'selector' => $this->parseSelector($value),
            default => [$value]
        };

        try {
            return implode('', [
                data_get($options, 'prepend', ''),
                call_user_func_array([$scraper, $method], $value)?->first(),
                data_get($options, 'append', ''),
            ]);
        } catch (DomSelectorException $e) {
            $this->logger->error('Error scraping URL', [
                'url' => $this->url,
                'error' => $e->getMessage(),
            ]);
            $this->errorNotification($e->getMessage());
        }

        return null;
    }

    protected function parseSelector(string $selector): array
    {
        if (! str_contains($selector, self::SELECTOR_ATTR_DELIMITER)) {
            return [$selector, 'text'];
        }

        // We get the attribute value from the selector assuming format is
        // .selector|attribute
        $parts = explode(self::SELECTOR_ATTR_DELIMITER, $selector);
        $attr = array_pop($parts);

        return [implode(self::SELECTOR_ATTR_DELIMITER, $parts), 'attr', [$attr]];
    }

    protected function errorNotification(string $message): void
    {
        Notification::make()
            ->title('Scrape error')
            ->body($message)
            ->danger()
            ->send();
    }
}
