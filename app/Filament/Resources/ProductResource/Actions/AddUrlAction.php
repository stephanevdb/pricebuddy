<?php

namespace App\Filament\Resources\ProductResource\Actions;

use App\Enums\Icons;
use App\Models\Product;
use App\Models\Url;
use App\Rules\StoreUrl;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;

class AddUrlAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'add_url';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Add URL'));

        $this->successNotificationTitle(__('URL Added'));

        $this->failureNotificationTitle(__('Unable to add URL'));

        $this->modalHeading(__('Add URL to this product'));

        $this->icon(Icons::Add->value);

        $this->form([
            TextInput::make('url')
                ->hiddenLabel(true)
                ->placeholder('http://my-store.com/product')
                ->rules([new StoreUrl]),
        ]);

        $this->color('gray');

        $this->keyBindings(['mod+a']);

        $this->action(function (array $data, Product $record): void {
            try {
                Url::createFromUrl($data['url'], $this->record->getKey(), auth()->id());

                $this->success();
            } catch (Exception $e) {
                $this->failure();
            }
        });
    }
}
