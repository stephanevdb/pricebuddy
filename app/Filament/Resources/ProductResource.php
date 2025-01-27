<?php

namespace App\Filament\Resources;

use App\Enums\Icons;
use App\Enums\StatusEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Providers\Filament\AdminPanelProvider;
use App\Rules\StoreUrl;
use App\Services\Helpers\CurrencyHelper;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?int $navigationSort = -1;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(fn ($livewire) => $livewire instanceof Pages\CreateProduct
                ? self::createForm($form)
                : self::editForm($form)
            );
    }

    public static function createForm(?Form $form = null, ?int $productId = null): array
    {
        $components = [];

        if (is_null($productId)) {
            $components[] = Forms\Components\Select::make('product_id')
                ->label('Existing product')
                ->searchable(['title'])
                ->getSearchResultsUsing(fn (string $search): array => auth()->user()->products()->where('title', 'like', "%{$search}%"
                )
                    ->limit(50)->pluck('title', 'id')
                    ->toArray()
                )
                ->hintIcon(Icons::Help->value, 'Add this URL to an existing product, leave empty to create a new product')
                ->nullable();
        }

        $components[] = TextInput::make('url')
            ->label('Product URL')
            ->hintIcon(Icons::Help->value, 'The domain of the URL must be in the list of available stores')
            ->rules([new StoreUrl]);

        return [
            Forms\Components\Section::make('Url of the product')->schema($components)
                ->description('Given the url we will scrape the product information'),
        ];
    }

    public static function editForm(Form $form): array
    {
        return [
            Forms\Components\Section::make('Basics')->schema([
                TextInput::make('title')
                    ->label('Product title')
                    ->hintIcon(Icons::Help->value, 'The name of the product'),

                TextInput::make('image')
                    ->label('Image Url')
                    ->hintIcon(Icons::Help->value, 'The Image URL of the product'),

                Forms\Components\Select::make('status')
                    ->options(StatusEnum::class)
                    ->default(StatusEnum::Published)
                    ->preload()
                    ->hintIcon(Icons::Help->value, 'Only published products get price history')
                    ->native(false),

                Select::make('tags')
                    ->relationship('tags', 'name')
                    ->createOptionForm([
                        TextInput::make('name')->required(),
                        Hidden::make('user_id')->default(auth()->id()),
                    ])
                    ->multiple()
                    ->nullable()
                    ->preload(),
            ])
                ->columns(2)
                ->description('Product info'),

            Forms\Components\Section::make('Notifications')->schema([
                TextInput::make('notify_price')
                    ->nullable()
                    ->suffix(CurrencyHelper::getSymbol())
                    ->hintIcon(Icons::Help->value, 'Get notified when price is equal or less than this value')
                    ->numeric(),

                TextInput::make('notify_percent')
                    ->nullable()
                    ->hintIcon(Icons::Help->value, 'Get notified when price drops below specified percentage')
                    ->suffix('%')
                    ->numeric(),
            ])
                ->columns(2)
                ->description('Notification settings'),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([

                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\ImageColumn::make('primary_image')
                            ->width(50)
                            ->height(50)
                            ->extraImgAttributes(['class' => 'rounded-md p-2 bg-white mr-2'])
                            ->label('Image')
                            ->url(fn ($record): string => $record->action_urls['view'])
                            ->grow(false),

                        Tables\Columns\Layout\Stack::make([
                            TextColumn::make('title')
                                ->searchable()
                                ->formatStateUsing(fn ($state): HtmlString => new HtmlString('<span title="'.$state.'">'.Str::limit($state, 50).'</span>'))
                                ->sortable()
                                ->weight(FontWeight::Bold)
                                ->extraAttributes(['class' => 'pr-4'])
                                ->url(fn (Product $record): string => $record->action_urls['view']),

                            TextColumn::make('tags')
                                ->color(Color::Gray)
                                ->formatStateUsing(fn ($record): string => $record->tags->pluck('name')->join(', '))
                                ->label('Tags')
                                ->url(null)
                                ->grow(false),
                        ]),
                    ])->extraAttributes(['class' => 'max-w-md']),

                    TextColumn::make('price_cache')
                        ->view('components.prices-column')
                        ->label('Current Prices')
                        ->url(null)
                        ->width('sm')
                        ->grow(false),

                    TextColumn::make('price_aggregates')
                        ->view('components.prices-aggregate-column')
                        ->label('Aggregates')
                        ->url(null)
                        ->grow(false),

                    Tables\Columns\Layout\Split::make([
                        self::getAggregateTableColumn('avg'),
                        self::getAggregateTableColumn('min'),
                        self::getAggregateTableColumn('max'),
                    ])->grow(false),

                    TextColumn::make('status')
                        ->badge()
                        ->sortable()->width('sm')
                        ->grow(false),

                ])->from('md'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(StatusEnum::class)
                    ->label('Status')
                    ->native(false),
                SelectFilter::make('lowest_in_period')
                    ->label('Current price is lowest in')
                    ->placeholder('All time')
                    ->options([
                        '7' => 'Last week',
                        '30' => 'Last month',
                        '90' => 'Last 90 days',
                        '365' => 'Last year',
                    ])
                    ->query(function (Builder $query, array $data): void {
                        if (! empty($data['value'])) {
                            $query->lowestPriceInDays($data['value']);
                        }
                    }),
                SelectFilter::make('tags')
                    ->relationship('tags', 'name')
                    ->label('Tags')
                    ->multiple()
                    ->native(false),
            ])
            ->paginated(AdminPanelProvider::DEFAULT_PAGINATION)
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                $query->currentUser()->with(['tags']);
            })
            ->recordUrl(null);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->where('user_id', auth()->id());
    }

    protected static function getAggregateTableColumn(string $method): TextColumn
    {
        return TextColumn::make($method.'_price')
            ->label(ucfirst($method))
            ->color(Color::Gray)
            ->formatStateUsing(fn (Product $record): string => strtoupper($method).' '.CurrencyHelper::toString($record->getPriceCacheAggregate($method)));
    }
}
