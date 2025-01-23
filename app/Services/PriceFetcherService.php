<?php

namespace App\Services;

use App\Jobs\UpdatePricesJob;
use App\Models\Product;
use App\Settings\AppSettings;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Sleep;

class PriceFetcherService
{
    protected array $config;

    protected bool $logging = false;

    public function __construct()
    {
        $this->config = config('price_scraper');
    }

    public static function new(): self
    {
        return resolve(static::class);
    }

    public function setLogging(bool $logging): self
    {
        $this->logging = $logging;

        return $this;
    }

    public function updateAllPrices(): void
    {
        Product::select('id')
            ->published()
            ->chunk(data_get($this->config, 'chunk_size'), function (EloquentCollection $productIds) {
                UpdatePricesJob::dispatch($productIds->pluck('id')->toArray());
            });
    }

    public function getProducts(array $productIds): EloquentCollection
    {
        return Product::whereIn('id', $productIds)->get();
    }

    public function updatePrices(array $productIds): EloquentCollection
    {
        return $this
            ->getProducts($productIds)
            ->each(function ($product) {
                /** @var Product $product */
                if ($this->logging) {
                    logger()->info("Updating price for product: '{$product->title}' (id: {$product->id})");
                }
                $product->updatePrices();
                Sleep::for(AppSettings::new()->sleep_seconds_between_scrape)->seconds();
            });
    }
}
