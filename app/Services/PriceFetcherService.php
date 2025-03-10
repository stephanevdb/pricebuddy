<?php

namespace App\Services;

use App\Jobs\UpdateAllPricesJob;
use App\Jobs\UpdateProductPricesJob;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class PriceFetcherService
{
    public const JOB_TIMEOUT = 1200; // 20 minutes

    protected array $config;

    protected bool $logging = false;

    public function __construct()
    {
        $this->config = config('price_buddy');
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
                UpdateAllPricesJob::dispatch($productIds->pluck('id')->toArray());
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
                UpdateProductPricesJob::dispatch($product, $this->logging);
            });
    }
}
