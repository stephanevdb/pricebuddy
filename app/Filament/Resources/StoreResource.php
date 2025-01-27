<?php

namespace App\Filament\Resources;

use App\Enums\Icons;
use App\Enums\ScraperService;
use App\Filament\Resources\StoreResource\Pages\CreateStore;
use App\Filament\Resources\StoreResource\Pages\EditStore;
use App\Filament\Resources\StoreResource\Pages\ListStores;
use App\Filament\Resources\StoreResource\Pages\TestStore;
use App\Models\Store;
use App\Providers\Filament\AdminPanelProvider;
use App\Rules\StoreUrl;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

class StoreResource extends Resource
{
    const DEFAULT_SELECTORS = [
        'title' => 'meta[property=og:title]|content',
        'price' => 'meta[property=og:price:amount]|content',
        'image' => 'meta[property=og:image]|content',
    ];

    protected static ?string $model = Store::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basics')->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->hintIcon(Icons::Help->value, 'The name of the store')
                        ->required(),

                    TextInput::make('initials')
                        ->label('Initials')
                        ->hintIcon(Icons::Help->value, 'Two characters to represent the store')
                        ->rules(['max:2']),
                ])
                    ->columns(2)
                    ->description('Store basic information')
                    ->live(),

                Forms\Components\Section::make('Domains')->schema([
                    Forms\Components\Repeater::make('domains')
                        ->schema([
                            TextInput::make('domain')->label('Domain'),
                        ])->required(),
                ])
                    ->description('This store will be used for these matching domains'),

                Forms\Components\Group::make([
                    Forms\Components\Section::make('Title strategy')->schema([
                        Forms\Components\Group::make(self::makeStrategyInput('title'))->columns(2),
                    ])->description('How to get the product title'),
                    Forms\Components\Section::make('Price strategy')->schema([
                        Forms\Components\Group::make(self::makeStrategyInput('price'))->columns(2),
                    ])->description('How to get the product price'),
                    Forms\Components\Section::make('Image strategy')->schema([
                        Forms\Components\Group::make(self::makeStrategyInput('image'))->columns(2),
                    ])->description('How to get the product image'),
                ])
                    ->label('Scrape Strategy')
                    ->statePath('scrape_strategy'),

                Forms\Components\Section::make('Scraper service')->schema([
                    Forms\Components\Radio::make('settings.scraper_service')
                        ->options(ScraperService::class)
                        ->descriptions([
                            ScraperService::Http->value => 'Faster and and less resource intensive. Use this for JSON strategy',
                            ScraperService::Api->value => 'Slower but good for scraping JavaScript rendered pages',
                        ])
                        ->reactive()
                        ->default(ScraperService::Http),

                    Forms\Components\Textarea::make('settings.scraper_service_settings')
                        ->label('Settings')
                        ->hint(new HtmlString('One option per line. <a href="https://github.com/amerkurev/scrapper" target="_blank">Read docs</a>'))
                        ->hidden(fn (Forms\Get $get) => $get('settings.scraper_service') !== ScraperService::Api->value)
                        ->rows(4)
                        ->placeholder("device=Desktop Firefox\nsleep=1000"),
                ])->description('Advanced scraper service settings')->columns(2),

            ])
            ->columns(1);
    }

    public static function testForm(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Test url scrape')->schema([
                TextInput::make('url')
                    ->label('Product URL')
                    ->hintIcon(Icons::Help->value, 'The URL to scrape')
                    ->required()
                    ->rules([new StoreUrl]),
            ])
                ->description('See the results of scraping a url using the current store settings')
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    TextColumn::make('initials')
                        ->formatStateUsing(fn (string $state): View => view(
                            'components.initials',
                            ['initials' => $state],
                        ))
                        ->width('7%')
                        ->label('')
                        ->grow(false)
                        ->alignCenter(),
                    TextColumn::make('name')
                        ->searchable()
                        ->sortable()
                        ->weight(FontWeight::Bold)
                        ->description(fn (Store $record): HtmlString => $record->domains_html),
                    TextColumn::make('settings.scraper_service')
                        ->label('Scraper')
                        ->badge()
                        ->sortable()
                        ->formatStateUsing(fn (string $state) => strtoupper($state))
                        ->color(fn (Store $record): array => ScraperService::tryFrom($record->scraper_service)->getColor())
                        ->grow(false),
                ]),

            ])
            ->paginated(AdminPanelProvider::DEFAULT_PAGINATION)
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('settings->scraper_service')
                    ->options(ScraperService::class)
                    ->label('Scraper'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStores::route('/'),
            'create' => CreateStore::route('/create'),
            'edit' => EditStore::route('/{record}/edit'),
            'test' => TestStore::route('/{record}/test'),
        ];
    }

    protected static function makeStrategyInput(string $key): array
    {
        return [
            Forms\Components\Select::make($key.'.type')
                ->label('Type')
                ->options([
                    'selector' => 'CSS Selector',
                    'regex' => 'Regex',
                    'json' => 'JSON path',
                ])
                ->required()
                ->default('selector')
                ->hintIcon(Icons::Help->value, 'How to get the value')
                ->live(),
            TextInput::make($key.'.value')
                ->label('Value')
                ->default(self::DEFAULT_SELECTORS[$key])
                ->required()
                ->hintIcon(Icons::Help->value, fn (Forms\Get $get) => match ($get($key.'.type')) {
                    'selector' => 'CSS selector to get the value. Use |attribute_name to get an attribute value instead of the element content',
                    'regex' => 'Regex pattern to get the value. Enclose the value in () to get the value',
                    'json' => 'JSON path to get the value. Use dot notation to get nested values',
                    default => ''
                })
                ->live(),
            TextInput::make($key.'.prepend')
                ->label('Prepend')
                ->hintIcon(Icons::Help->value, 'Optionally prepend a static value to the extracted value'),
            TextInput::make($key.'.append')
                ->label('Append')
                ->hintIcon(Icons::Help->value, 'Optionally append a static value to the extracted value'),
        ];
    }
}
