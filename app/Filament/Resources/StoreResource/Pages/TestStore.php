<?php

namespace App\Filament\Resources\StoreResource\Pages;

use App\Filament\Resources\StoreResource;
use App\Filament\Resources\StoreResource\Widgets\TestResultsWidget;
use App\Models\Store;
use App\Services\ScrapeUrl;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class TestStore extends EditRecord
{
    protected static string $resource = StoreResource::class;

    protected static ?string $title = 'Test Store';

    protected function getForms(): array
    {
        return [
            'form' => $this->form(static::getResource()::testForm(
                $this->makeForm()
                    ->operation('test')
                    ->model($this->getRecord())
                    ->statePath($this->getFormStatePath())
                    ->columns($this->hasInlineLabels() ? 1 : 2)
                    ->inlineLabel($this->hasInlineLabels()),
            )),
        ];
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->authorizeAccess();

        /** @var Store $store */
        $store = $this->getRecord();
        $url = data_get($this->data, 'url', '');

        $scrape = ScrapeUrl::new($url)->scrape(['store' => $store, 'use_cache' => false]);

        session()->put('test_scrape', $scrape);
    }

    public function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label('Test url scrape')
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('edit')->url(fn () => StoreResource::getUrl('edit', ['record' => $this->record]))->label('Edit'),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('test');
    }

    protected function getFooterWidgets(): array
    {
        return [
            TestResultsWidget::class,
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
