<?php

namespace App\Filament\Resources\ProductResource\Actions;

use App\Enums\Icons;
use App\Models\Product;
use App\Models\SearchResultUrl;
use App\Models\Url;
use Exception;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class AddSearchResultUrlAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'add_search_url';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Add URL'));

        $this->successNotificationTitle(__('URL Added'));

        $this->failureNotificationTitle(__('Unable to add URL'));

        $this->icon(Icons::Add->value);

        $this->color('primary');

        $this->visible(function (SearchResultUrl $record) {
            return ! is_null($record->store_id);
        });
    }

    public function setProduct(?Product $product): self
    {
        $this->action(function (array $data, SearchResultUrl $record, Table $table) use ($product): void {
            try {
                $url = Url::createFromUrl($record->url, $product?->getKey(), auth()->id());

                if ($url->product) {
                    $this->success();
                    $this->redirect($url->product->view_url);
                } else {
                    $this->failure();
                }
            } catch (Exception $e) {
                $this->failure();
            }
        });

        return $this;
    }
}
