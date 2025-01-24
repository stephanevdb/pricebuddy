<?php

namespace App\Filament\Widgets;

use App\Dto\PriceCacheDto;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductStatsOverview extends BaseWidget
{
    protected static ?int $sort = 10;

    public static function canView(): bool
    {
        return Product::published()->currentUser()->count() > 0;
    }

    protected function getColumns(): int
    {
        $count = count($this->getCachedStats());

        if ($count <= 2) {
            return 2;
        }

        return 3;
    }

    protected function getStats(): array
    {
        $products = Product::latest()
            ->currentUser()
            ->published()
            ->get()
            ->filter(fn (Product $product) => isset($product->price_cache[0]))
            ->map(function (Product $product) {
                /** @var PriceCacheDto $lowest */
                $lowest = $product->getPriceCache()->first();

                return Stat::make($product->title(40), $lowest->getPriceFormatted())
                    ->description($lowest->getTrendText())
                    ->descriptionIcon($lowest->getTrendIcon())
                    ->chart(
                        $lowest->getHistory(10)->values()->toArray()
                    )
                    ->color($lowest->getTrendColor())
                    ->url($product->action_urls['view']);
            })->values();

        return $products->toArray();
    }
}
