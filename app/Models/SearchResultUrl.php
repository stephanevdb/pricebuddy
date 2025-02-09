<?php

namespace App\Models;

use App\Enums\IntegratedServices;
use App\Services\Helpers\IntegrationHelper;
use App\Services\Helpers\SettingsHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Sushi\Sushi;

/**
 * @property string $title
 * @property string $url
 * @property string $snippet
 * @property string $thumbnail
 * @property string $domain
 * @property string $cache_key
 * @property ?int $store_id
 * @property ?int $relevance
 */
class SearchResultUrl extends Model
{
    use Sushi;

    public static ?string $searchQuery = null;

    protected $schema = [
        'title' => 'string',
        'url' => 'string',
        'snippet' => 'text',
        'thumbnail' => 'text',
        'domain' => 'string',
        'store_id' => 'integer',
        'relevance' => 'integer',
    ];

    public function getSettings(): array
    {
        return SettingsHelper::getSetting(
            'integrated_services.'.IntegratedServices::SearXng->value,
            []
        );
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /** Cache result for specific $searchQuery */
    protected function cacheKey(): Attribute
    {
        return Attribute::make(
            get: fn () => sprintf('search_result_%s', Str::slug(self::$searchQuery ?? '_empty', '_')),
        );
    }

    public static function setProductSearchQuery(?string $searchQuery): void
    {
        self::$searchQuery = ! is_null($searchQuery)
            ? trim(data_get(IntegrationHelper::getSearchSettings(), 'search_prefix').' '.$searchQuery)
            : null;
    }

    public function scopeTitleFilter($query, ?string $title)
    {
        return $this->searchQuery($title);
    }

    /** This will set up the $searchQuery value before querying the results  */
    public function searchQuery(?string $searchQuery)
    {
        self::setProductSearchQuery($searchQuery);

        // @phpstan-ignore-next-line
        $cacheKey = (new static)->cache_key;

        /**
         * Not only it will avoid recreating the same table, but also
         * will create a new table for any $searchQuery value.
         */
        if (Cache::get($cacheKey) == null) {
            $this->migrate();
        }

        return self::query();
    }

    public function getRows()
    {
        /** This will launch an exception if not queried using searchQuery() */
        if (self::$searchQuery == null) {
            return [];
        }

        $rows = Cache::remember(
            $this->cache_key,
            now()->addHour(),
            function () {
                $results = Http::get(data_get($this->getSettings(), 'url'), [
                    'format' => 'json',
                    'q' => self::$searchQuery,
                ])->json('results');

                return $this->parseSearchResults($results);
            }
        );

        $this->addStoresToResults($rows);

        return $rows;
    }

    protected function parseSearchResults(array $results): array
    {
        return collect($results)->map(function ($result, $idx) {
            return [
                'title' => $result['title'],
                'url' => $result['url'],
                'snippet' => $result['content'],
                'thumbnail' => $result['thumbnail'] ?? null,
                'domain' => parse_url($result['url'], PHP_URL_HOST),
                'relevance' => $idx,
            ];
        })->toArray();
    }

    protected function addStoresToResults(array &$results): void
    {
        $resultsCollection = collect($results);

        // Populate stores.
        $domains = $resultsCollection->pluck('domain')->toArray();
        $stores = Store::query()->select('id', 'domains')->domainFilter($domains)->get();

        $results = $resultsCollection->map(function ($result) use ($stores) {
            $store = $stores->filter(fn ($store) => $store->hasDomain($result['domain']))->first();
            $result['store_id'] = $store->id ?? null;

            return $result;
        })->toArray();
    }
}
