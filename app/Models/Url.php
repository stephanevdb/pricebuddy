<?php

namespace App\Models;

use App\Services\Helpers\CurrencyHelper;
use App\Services\ScrapeUrl;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

/**
 * @property ?string $url
 * @property string $product_name_short
 * @property string $store_name
 * @property string $product_url
 * @property string $average_price
 * @property string $latest_price_formatted
 * @property ?Store $store
 * @property ?Product $product
 * @property ?int $store_id
 */
class Url extends Model
{
    /** @use HasFactory<\Database\Factories\UrlFactory> */
    use HasFactory;

    public static function booted()
    {
        static::deleted(function (Url $url) {
            $url->prices()->delete();
            $url->product->updatePriceCache();
        });
    }

    protected $guarded = [];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class)->latest('created_at');
    }

    public function latestPrice(): HasMany
    {
        return $this->prices()->limit(1);
    }

    public function scrape(): array
    {
        return $this->url ? ScrapeUrl::new($this->url)->scrape() : [];
    }

    public static function createFromUrl(string $url, ?int $productId = null, ?int $userId = null): Url|false
    {
        $scrape = ScrapeUrl::new($url)->scrape();
        $userId = $userId ?? auth()->id();

        /** @var ?Store $store */
        $store = data_get($scrape, 'store');

        if (! $store || ! data_get($scrape, 'title')) {
            return false;
        }

        if (is_null($productId)) {
            if (! $userId) {
                throw new AuthorizationException('User is required to create a product.');
            }

            $productId = Product::create([
                'title' => data_get($scrape, 'title'),
                'image' => data_get($scrape, 'image'),
                'user_id' => $userId,
            ])->id;
        }

        /** @var Url $urlModel */
        $urlModel = self::create([
            'url' => $url,
            'store_id' => $store->getKey(),
            'product_id' => $productId,
        ]);

        $urlModel->updatePrice(data_get($scrape, 'price'));

        return $urlModel;
    }

    public function updatePrice(int|float|string|null $price = null): Price|Model|null
    {
        if (! $this->store_id) {
            return null;
        }

        if (is_null($price) || $price === '') {
            $price = data_get($this->scrape(), 'price');
        }

        if (is_null($price)) {
            return null;
        }

        return $this->prices()->create([
            'price' => CurrencyHelper::toFloat($price),
            'store_id' => $this->store_id,
        ]);
    }

    public function productNameShort(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->product->title_short ?? 'Unknown'
        );
    }

    public function storeName(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::limit(($this->store->name ?? 'Missing store'), 100)
        );
    }

    protected function productUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->product?->action_urls['view'] ?? '/'
        );
    }

    protected function latestPriceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => Number::currency($this->latestPrice()->first()->price ?? 0)
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
}
