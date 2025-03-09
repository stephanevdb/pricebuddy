<?php

namespace App\Jobs;

use App\Models\Url;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class UpdatePriceJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function __construct(protected Url $url) {}

    public function handle(): void
    {
        try {
            $this->url->updatePrice();
        } catch (Exception $e) {
        }
    }
}
