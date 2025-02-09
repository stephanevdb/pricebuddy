<?php

namespace App\Filament\Resources\ProductResource\Actions;

use App\Enums\Icons;
use App\Models\Product;
use App\Models\SearchResultUrl;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class IgnoreSearchResultUrlAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'ignore_search_url';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Ignore'));

        $this->successNotificationTitle(__('URL Ignored'));

        $this->failureNotificationTitle(__('Unable to ignore URL'));

        $this->icon(Icons::Delete->value);

        $this->color('danger');
    }

    public function setProduct(Product $product): self
    {
        $this->action(function (array $data, SearchResultUrl $record, Table $table) use ($product): void {

            $product->update(['ignored_urls' => collect($product->ignored_urls)
                ->push($record->url)
                ->unique()
                ->values()
                ->all()]);

            $this->success();
            $this->dispatch('ResetProductSearchTable');
        });

        return $this;
    }
}
