<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Filament\Resources\ProductResource\Actions\AddSearchResultStoreAction;
use App\Filament\Resources\ProductResource\Actions\AddSearchResultUrlAction;
use App\Models\SearchResultUrl;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CreateViaSearchTable extends BaseWidget
{
    protected $listeners = [
        'updateCreateViaSearchTable' => 'reRenderTable',
        'emptyCreateViaSearchTable' => 'emptyRenderTable',
    ];

    public ?string $searchQuery = null;

    public static function canView(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        SearchResultUrl::setProductSearchQuery($this->searchQuery);

        return $table
            ->heading('Search results for "'.$this->searchQuery.'"')
            ->query(
                SearchResultUrl::query()->with('store')
                    ->orderByDesc('store_id')
                    ->orderByDesc('relevance')
            )
            ->columns(ProductSearch::tableColumns())
            ->actions([
                AddSearchResultUrlAction::make()
                    ->setProduct(null),
                AddSearchResultStoreAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public function reRenderTable(?string $searchQuery): void
    {
        $this->searchQuery = $searchQuery;

        $this->resetTable();
    }

    public function emptyRenderTable(): void
    {
        $this->searchQuery = null;

        $this->resetTable();
    }
}
