<?php

namespace App\Jobs;

use App\Services\PriceFetcherService;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdatePricesJob
{
    use Dispatchable;

    public function __construct(protected array $productIds = []) {}

    public function handle(): void
    {
        PriceFetcherService::new()->updatePrices($this->productIds);
    }
}
