<?php

namespace App\Filament\Resources\ProductResource\Actions;

use App\Enums\Icons;
use App\Models\SearchResultUrl;
use Filament\Tables\Actions\Action;

class AddSearchResultStoreAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'add_store';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Add Store'));

        $this->icon(Icons::Add->value);

        $this->color('gray');

        $this->visible(function (SearchResultUrl $record) {
            return is_null($record->store_id);
        });

        $this->url(route('filament.admin.resources.stores.create'));
    }
}
