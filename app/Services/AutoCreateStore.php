<?php

namespace App\Services;

use App\Enums\ScraperService;
use App\Models\Store;
use App\Services\Helpers\CurrencyHelper;
use Closure;
use Exception;
use Illuminate\Support\Uri;
use Jez500\WebScraperForLaravel\Exceptions\DomSelectorException;
use Jez500\WebScraperForLaravel\Facades\WebScraper;
use Symfony\Component\DomCrawler\Crawler;

class AutoCreateStore
{
    protected array $titleSelectors = [
        'meta[property="og:title"]|content',
        'title',
        'h1',
    ];

    protected array $titleRegexes = [];

    protected array $priceSelectors = [
        'meta[property="product:price:amount"]|content',
        '[itemProp="price"]|content',
        '.price',
        '[class^="price"]',
        '[class*="price"]',
    ];

    protected array $priceRegexes = [
        '~\"price\"\:\s?\"(.*?)\"~', // Something that looks like a price, in a json object, eg "price": "99.99"
        '~>\$(\d+(\.\d{2})?)<~', // Something that looks like a price, in a tag, eg >$99.99<
        '~\$(\d+(\.\d{2})?)~', // Something that looks like a price, not in a tag
    ];

    protected array $imageSelectors = [
        'meta[property="og:image"]|content',
        'meta[property="og:image:secure_url"]|content',
        'img[src]|src',
    ];

    protected array $imageRegexes = [
        '~\"image\"\:\s?\"(.*?\.jpg)\"~', // Something that looks like an image, in a json object, eg "price": "99.99"
        '~\"image\"\:\s?\"(.*?\.png)\"~', // Something that looks like an image, in a json object, eg "price": "99.99"
    ];

    public function __construct(protected string $url, protected ?string $html = null)
    {
        if (empty($html)) {
            $this->html = WebScraper::http()
                ->from($url)
                ->get()
                ->getBody();
        }
    }

    public static function canAutoCreateFromUrl(string $url): bool
    {
        $self = new self($url);

        return ! is_null($self->getStoreAttributes());
    }

    public static function createStoreFromUrl(string $url): ?Store
    {
        // Check if store exists.
        $host = strtolower(Uri::of($url)->host());

        if ($existing = Store::query()->domainFilter($host)->first()) {
            return $existing;
        }

        $self = new self($url);
        $attributes = $self->getStoreAttributes();

        return $attributes
            ? Store::create($attributes)
            : null;
    }

    public function getStoreAttributes(): ?array
    {
        $strategy = $this->strategyParse();

        // Exit if required fields are missing.
        if (empty($strategy['title']['value']) || empty($strategy['price']['value'])) {
            logger()->error('Unable to auto create store', [
                'url' => $this->url,
                'strategy' => $strategy,
            ]);

            return null;
        }

        $attributes = [
            'user_id' => auth()->id(),
        ];

        $host = strtolower(Uri::of($this->url)->host());

        if (str_starts_with($host, 'www.')) {
            $host = substr($host, 4);
        }

        $attributes['domains'] = [
            ['domain' => $host],
            ['domain' => 'www.'.$host],
        ];

        $attributes['name'] = ucfirst(collect(explode('.', $host))->first());

        $attributes['scrape_strategy'] = collect($this->strategyParse())
            ->mapWithKeys(function ($value, $key) {
                return [
                    $key => collect($value)->only('type', 'value')->all(),
                ];
            })
            ->toArray();

        $attributes['settings'] = [
            'scraper_service' => ScraperService::Http->value,
            'scraper_service_settings' => '',
            'test_url' => $this->url,
        ];

        return $attributes;
    }

    public function strategyParse(): array
    {
        return [
            'title' => $this->parseTitle(),
            'price' => $this->parsePrice(),
            'image' => $this->parseImage(),
        ];
    }

    protected function parseTitle(): ?array
    {
        if ($match = $this->attemptSelectors($this->titleSelectors)) {
            return $match;
        }

        if ($match = $this->attemptRegex($this->titleRegexes)) {
            return $match;
        }

        return [];
    }

    protected function parsePrice(): ?array
    {
        $validateCallback = function ($value) {
            return CurrencyHelper::toFloat($value);
        };

        if ($match = $this->attemptSelectors($this->priceSelectors, $validateCallback)) {
            return $match;
        }

        if ($match = $this->attemptRegex($this->priceRegexes, $validateCallback)) {
            return $match;
        }

        return [];
    }

    protected function parseImage(): ?array
    {
        if ($match = $this->attemptSelectors($this->imageSelectors)) {
            return $match;
        }

        if ($match = $this->attemptRegex($this->imageRegexes)) {
            return $match;
        }

        return [];
    }

    protected function attemptSelectors(array $selectors, ?Closure $validateValue = null): ?array
    {
        $value = null;
        $workingSelector = null;

        $dom = new Crawler($this->html);

        foreach ($selectors as $selector) {
            if ($value) {
                break;
            }

            $selectorSettings = ScrapeUrl::parseSelector($selector);
            $realSelector = $selectorSettings[0];
            $method = $selectorSettings[1] ?? 'text';
            $args = $selectorSettings[2] ?? [];

            try {
                $results = $dom->filter($realSelector)
                    ->each(function (Crawler $node) use ($method, $args, $validateValue) {
                        $extracted = call_user_func_array([$node, $method], $args);

                        return is_null($validateValue)
                            ? $extracted
                            : $validateValue($extracted);
                    });

                $value = data_get($results, '0');

                if ($value) {
                    $workingSelector = $selector;
                }
            } catch (DomSelectorException $e) {
                // not found.
            }
        }

        return ! empty($workingSelector)
            ? ['type' => 'selector', 'value' => $workingSelector, 'data' => $value]
            : null;
    }

    protected function attemptRegex(array $regexes, ?Closure $validateValue = null): ?array
    {
        $value = null;
        $workingRegex = null;

        foreach ($regexes as $regex) {
            if ($value) {
                break;
            }

            try {
                preg_match_all($regex, $this->html, $matches);
                $extracted = data_get($matches, '1.0');

                $value = is_null($validateValue)
                    ? $extracted
                    : $validateValue($extracted);

                if ($value) {
                    $workingRegex = $regex;
                }
            } catch (Exception $e) {
            }
        }

        return ! empty($workingRegex)
            ? ['type' => 'regex', 'value' => $workingRegex, 'data' => $value]
            : null;
    }
}
