<?php

namespace App\Filament\Widgets;

use App\Enums\Icons;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Str;

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
                $lowest = collect($product->price_cache)->sortBy('price')->first();

                $icon = Icons::TrendNone->value;
                $color = 'warning';
                $trend = 'No change';

                if (! is_null($lowest['trend'])) {
                    $icon = Icons::getTrendIcon($lowest['trend']);
                    $color = $lowest['trend_color'];
                    $trend = $lowest['trend'] === 'up'
                        ? 'Price increase'
                        : 'Price decrease';
                }

                return Stat::make(Str::limit($product->title, 40), $lowest['price'])
                    ->description($trend)
                    ->descriptionIcon($icon)
                    ->chart(
                        collect($lowest['history'])->values()->reverse()->take(10)
                            ->reverse()->values()->toArray()
                    )
                    ->color($color)
                    ->url($product->action_urls['view']);
            })->values();

        return $products->toArray();
    }
}
