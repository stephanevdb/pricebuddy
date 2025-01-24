<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Dto\PriceCacheDto;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class ProductUrlStats extends BaseWidget
{
    protected static ?int $sort = 10;

    public Model|Product|null $record = null;

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        /** @var Product $product */
        $product = $this->record;

        $products = $product->getPriceCache()
            ->map(function (PriceCacheDto $cache, $idx) {
                return PriceStat::make(
                    '@ '.$cache->getStoreName().($idx === 0 ? ' (Lowest price)' : ''),
                    $cache->getPriceFormatted()
                )
                    ->setPriceCache($idx, $cache);
            })->values();

        return $products->toArray();
    }
}
