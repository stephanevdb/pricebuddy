<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\SearchResultUrl;
use App\Services\Helpers\IntegrationHelper;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;

/**
 * @property Form $form
 */
class CreateViaSearchForm extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.resources.product-resource.widgets.create-via-search';

    public ?array $data = [];

    public ?string $searchQuery = null;

    public static function canView(): bool
    {
        return IntegrationHelper::isSearchEnabled();
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Search via the web')
                    ->description('Search for a product on the web and create a new product from the search results.')
                    ->columns(1)
                    ->schema([
                        TextInput::make('keyword')
                            ->suffixAction(
                                Action::make('copyCostToPrice')
                                    ->icon('heroicon-m-magnifying-glass')
                                    ->requiresConfirmation()
                                    ->submit('form')
                            ),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->validate();

        $this->searchQuery = $this->data['keyword'];

        // Cache the results, this makes the loading spinner show for appropriate amount of time
        SearchResultUrl::$searchQuery = $this->searchQuery;
        SearchResultUrl::query()->get();

        $this->dispatch('updateCreateViaSearchTable', $this->searchQuery);
    }
}
