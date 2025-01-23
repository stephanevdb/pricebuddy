<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

/**
 * @property ?string $title
 * @property ?string $title_short
 * @property string $primary_image
 * @property ?string $image
 * @property array $action_urls
 * @property array $price_cache
 * @property string $average_price
 * @property Collection $current_prices
 * @property Collection $urls
 * @property ?float $notify_price
 * @property ?float $notify_percent
 * @property ?User $user
 * @property int $user_id
 */
class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $guarded = [
        'price',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
        'price_cache' => 'array',
    ];

    public static function booted()
    {
        static::deleted(function (Product $product) {
            // Delete all related urls, should cascade to prices.
            $product->urls()->delete();
        });
    }

    /***************************************************
     * Relationships.
     **************************************************/

    /**
     * Product has many urls.
     */
    public function urls(): HasMany
    {
        return $this->hasMany(Url::class);
    }

    /**
     * Product has many users.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Product has many prices through urls.
     */
    public function prices(): HasManyThrough
    {
        return $this->hasManyThrough(
            Price::class,
            Url::class,
            'product_id', // Foreign key on the urls table...
            'url_id', // Foreign key on the prices table...
            'id', // Local key on the product table...
            'id' // Local key on the prices table...
        );
    }

    /**
     * Product has many stores through urls.
     */
    public function stores(): HasManyThrough
    {
        return $this->hasManyThrough(
            Store::class,
            Url::class,
            'product_id', // Foreign key on the urls table...
            'id', // Foreign key on the store table...
            'id', // Local key on the product table...
            'store_id' // Local key on the stores table...
        );
    }

    /***************************************************
     * Scopes.
     **************************************************/

    /**
     * Scope to only published products.
     */
    public function scopePublished(EloquentBuilder $query): EloquentBuilder
    {
        return $query->where('status', StatusEnum::Published);
    }

    /**
     * Scope to only current user.
     */
    public function scopeCurrentUser(EloquentBuilder $query): EloquentBuilder
    {
        return $query->where('user_id', auth()->id());
    }

    /***************************************************
     * Attributes.
     **************************************************/

    /**
     * Current price cache for table column.
     */
    public function currentPricesColumn(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return new HtmlString(collect($this->price_cache)
                    ->map(fn ($price) => $price['price'].' ('.$price['store_name'].')'
                    )
                    ->implode('<br />'));
            },
        );
    }

    /**
     * Price trend for lowest priced store.
     */
    public function trend(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return collect($this->price_cache)
                    ->pluck('trend')
                    ->first();
            },
        );
    }

    /**
     * Price trend for lowest priced store.
     */
    public function averagePrice(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $avg = $this->prices()->avg('price') ?? 0;

                return Number::currency(round($avg, 2));
            },
        );
    }

    /**
     * Get main image with fallback.
     */
    public function getPrimaryImageAttribute(): string
    {
        return empty($this->image)
            ? asset('/images/placeholder.png')
            : $this->image;
    }

    /**
     * Short version of the product title.
     */
    public function titleShort(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::limit($this->title, 20)
        );
    }

    /**
     * Get key urls for the product.
     */
    public function getActionUrlsAttribute(): array
    {
        return $this->getKey()
            ? [
                'edit' => route('filament.admin.resources.products.edit', $this),
                'view' => route('filament.admin.resources.products.view', $this),
                'fetch' => route('filament.admin.resources.products.fetch', $this),
            ]
            : [];
    }

    /***************************************************
     * Helpers.
     **************************************************/

    /**
     * Build a price cache array.
     */
    public function buildPriceCache(): Collection
    {
        $history = $this->getPriceHistory();
        $stores = Store::findMany($history->keys());

        return $stores
            ->map(function ($store) use ($history): array {
                /** @var Store $store */
                /** @var Collection $storeHistory */
                $storeHistory = $history->get($store->getKey());

                /** @var ?Url $url */
                $url = $this->urls()->where('store_id', $store->getKey())->oldest('id')->first();

                // Build trend.
                $lastTwo = $storeHistory->values()->reverse()->take(2)->values()->toArray();
                $trend = null;
                if (count($lastTwo) === 2 && $lastTwo[0] !== $lastTwo[1]) {
                    $trend = ($lastTwo[0] - $lastTwo[1]) > 0 ? 'up' : 'down';
                }

                // Build output.
                return [
                    'store_id' => $store->getKey(),
                    'store_name' => $store->name,
                    'url_id' => $url?->getKey(),
                    'url' => $url?->url,
                    'trend' => $trend,
                    'trend_color' => match ($trend) {
                        'down' => 'success',
                        'up' => 'danger',
                        default => 'warning',
                    },
                    'price' => Number::currency($storeHistory->last()),
                    'price_raw' => $storeHistory->last(),
                    'history' => $storeHistory->toArray(),
                ];
            })
            ->sortBy('price')
            ->values();
    }

    public function getAllPricesQuery(): Builder
    {
        return DB::table('prices')
            ->select(
                'prices.id',
                'prices.price',
                'prices.created_at',
                'urls.id as url_id',
                'urls.store_id'
            )
            ->join('urls', 'prices.url_id', '=', 'urls.id')
            ->where('urls.product_id', $this->id)
            ->orderByDesc('prices.created_at');
    }

    /**
     * Update all prices for this product.
     */
    public function updatePrices(): void
    {
        $this->urls->each(function (Url $url) {
            $url->updatePrice();
        });

        $this->updatePriceCache();
    }

    /**
     * Update the price cache for this product.
     */
    public function updatePriceCache(): void
    {
        $priceCache = $this->buildPriceCache()->toArray();
        $this->update(['price_cache' => $priceCache]);
    }

    /**
     * Get the price history for this product.
     */
    public function getPriceHistory(): Collection
    {
        return $this->getAllPricesQuery()
            ->whereDate('prices.created_at', '>=', now()->subYear())
            ->where('prices.price', '>', 0)
            ->orderBy('prices.created_at')
            ->get()
            ->groupBy('store_id')
            ->mapWithKeys(function ($prices, int $storeId) {
                // All price entries for the store.
                $storeHistory = $prices->sortBy('created_at')
                    ->groupBy('created_at')
                    ->mapWithKeys(fn ($prices, string $date) => [
                        Carbon::parse($date)->toDateString() => $prices->pluck('price')->min(),
                    ]);

                // If only one price, extend back one day.
                if ($storeHistory->count() === 1) {
                    $storeHistory = $this->extendSinglePriceHistory($storeHistory);
                }

                return [$storeId => $storeHistory->sortKeys()];
            });
    }

    /**
     * If we only have one price history, extend it back one day.
     */
    protected function extendSinglePriceHistory(Collection $history): Collection
    {
        return $history->put(
            Carbon::parse($history->keys()->first())->subDay()->toDateString(),
            $history->values()->first()
        );
    }

    /**
     * Get the cached price history for this product.
     */
    public function getPriceHistoryCached(): Collection
    {
        return collect($this->price_cache)
            ->mapWithKeys(fn ($price) => [
                $price['store_id'] => collect($price['history']),
            ]);
    }

    public function shouldNotifyOnPrice(float $price): bool
    {
        // Check if price is less than notify price.
        if (! empty($this->notify_price) && (float) $this->notify_price <= $price) {
            return true;
        }

        if (! empty($this->notify_percent)) {
            // Get first price.
            /** @var ?Price $firstPriceModel */
            $firstPriceModel = $this->prices()->oldest('prices.created_at')->first();
            $firstPrice = $firstPriceModel?->price;

            // No first price, can't calculate percent.
            if (is_null($firstPrice)) {
                return false;
            }

            // Calculate percent.
            $percent = ($price - $firstPrice) / $firstPrice * 100;

            // Check if percent is greater than notify percent.
            return $percent >= $this->notify_percent;
        }

        return false;
    }
}
