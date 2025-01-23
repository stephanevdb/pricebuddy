<?php

namespace App\Listeners;

use App\Events\PriceCreatedEvent;
use App\Notifications\PriceAlertNotification;

class PriceCreatedListener
{
    public function __construct() {}

    public function handle(PriceCreatedEvent $event): void
    {
        // Need a product proceed.
        if (! $product = $event->price->product) {
            return;
        }

        // Need a url proceed.
        if (! $url = $event->price->url) {
            return;
        }

        // Notify the user if the price is within the range.
        if ($product->shouldNotifyOnPrice($event->price->price)) {
            $product->user?->notify(new PriceAlertNotification($url));
        }
    }
}
