<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class UrlsTableWidget extends BaseWidget
{
    public Model|Product|null $record = null;

    public function table(Table $table): Table
    {
        /** @var Product $product */
        $product = $this->record;

        return $table
            ->heading('Product Urls')
            ->query(
                $product->urls()->with('store')->getQuery()
            )
            ->columns([
                Tables\Columns\TextColumn::make('store.name')
                    ->label('Store'),
                Tables\Columns\TextColumn::make('url')
                    ->label('Url'),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
