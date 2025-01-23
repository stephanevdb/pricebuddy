<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ProductResource\Widgets\PriceHistoryChart;
use App\Models\Product;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property Product $record
 */
class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected static string $view = 'filament.pages.product.view';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title;
    }

    protected function getFooterWidgets(): array
    {
        return [
            PriceHistoryChart::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return [
            'sm' => 1,
            'xl' => 1,
        ];
    }
}
