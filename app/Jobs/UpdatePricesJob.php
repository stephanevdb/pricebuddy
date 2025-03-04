<?php

namespace App\Jobs;

use App\Services\PriceFetcherService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class UpdatePricesJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function __construct(protected array $productIds = []) {}

    public function handle(): void
    {
        PriceFetcherService::new()
            ->setLogging(true)
            ->updatePrices($this->productIds);
    }
}
