<?php

namespace App\Filament\Resources\StoreResource\Widgets;

use App\Models\Store;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class TestResultsWidget extends Widget
{
    public Model|Store|null $record = null;

    protected static string $view = 'filament.resources.store-resource.widgets.test-results-widget';

    public static function canView(): bool
    {
        return session()->has('test_scrape');
    }

    protected function getViewData(): array
    {
        return [
            'scrape' => session()->pull('test_scrape'),
            'record' => $this->record,
        ];
    }
}
