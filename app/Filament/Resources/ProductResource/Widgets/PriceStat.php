<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Dto\PriceCacheDto;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Contracts\View\View;

class PriceStat extends Stat
{
    public ?PriceCacheDto $priceCache = null;

    public int $idx = 0;

    public function render(): View
    {
        return view('filament.pages.product.price-stat', $this->data());
    }

    public function setPriceCache(int $idx, PriceCacheDto $cache): self
    {
        $this->idx = $idx;
        $this->priceCache = $cache;

        parent::description($cache->getTrendText());
        parent::descriptionIcon($cache->getTrendIcon());
        parent::color($cache->getTrendColor());
        parent::url($cache->getUrl(), true);

        parent::chart(
            $cache->getHistory()
                ->values()->reverse()->take(10)
                ->reverse()->values()->toArray()
        );

        return $this;
    }

    public function data()
    {
        return array_merge(parent::data(), [
            'trend' => $this->priceCache,
            'idx' => $this->idx,
        ]);
    }
}
