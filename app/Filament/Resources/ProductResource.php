<?php

namespace App\Filament\Resources;

use App\Enums\Icons;
use App\Enums\StatusEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Rules\StoreUrl;
use App\Services\CurrencyService;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
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

    protected static function createForm(Form $form): array
    {
        return [

            Forms\Components\Section::make('Url of the product')->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Existing product')
                    ->searchable(['title'])
                    ->getSearchResultsUsing(fn (string $search): array => auth()->user()->products()->where('title', 'like', "%{$search}%"
                    )
                        ->limit(50)->pluck('title', 'id')
                        ->toArray()
                    )
                    ->hintIcon(Icons::Help->value, 'Add this URL to an existing product, leave empty to create a new product')
                    ->nullable(),

                TextInput::make('url')
                    ->label('Product URL')
                    ->hintIcon(Icons::Help->value, 'The domain of the URL must be in the list of available stores')
                    ->rules([new StoreUrl]),
            ])
                ->description('Given the url we will scrape the product information'),
        ];
    }

    protected static function editForm(Form $form): array
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
            ])
                ->columns(2)
                ->description('Product info'),

            Forms\Components\Section::make('Notifications')->schema([
                TextInput::make('notify_price')
                    ->nullable()
                    ->suffix(CurrencyService::getSymbol())
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
                Tables\Columns\ImageColumn::make('primary_image')
                    ->width(50)
                    ->height(50)
                    ->extraImgAttributes(['class' => 'rounded-md p-2 bg-white'])
                    ->label('Image'),

                TextColumn::make('title')
                    ->searchable()
                    ->formatStateUsing(fn ($state): HtmlString => new HtmlString('<span title="'.$state.'">'.Str::limit($state, 50).'</span>'))
                    ->sortable(),

                TextColumn::make('price_cache')
                    ->view('components.prices-column')
                    ->label('Current Prices')
                    ->url(null),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->orderByDesc('id')
                    ->currentUser();
            })
            ->recordUrl(fn ($record) => $record->action_urls['view']);
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
}
