@php
    use App\Enums\Trend;
    use function Filament\Support\get_color_css_variables;

    /** @var \App\Models\Product $product */
    /** @var \App\Dto\PriceCacheDto $latestPrice */
    $latestPrice = $product->getPriceCache()->first();

@endphp
<div
    class="display-block w-full rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
    style="{{ Filament\Support\get_color_css_variables(Trend::getColor($product->trend), shades: [300, 500, 400, 600, 800]) }}"
>
    <a class="flex gap-2" href="{{ $product->view_url }}">
        <div class="w-20 h-20 min-w-20 m-2 rounded-md overflow-hidden p-1 bg-white flex items-center">
            <img src="{{ $product->primary_image }}" alt="{{ $product->title }}"
                 class="rounded-md display-block h-auto block w-20"/>
        </div>
        <div class="my-1 flex flex-col min-w-0 justify-center" style="width: calc(100% - 5rem)">
            <h3
                class="mb-1 text-sm text-gray-500 dark:text-gray-400 font-bold truncate min-w-0"
                style="max-width: 13rem"
                title="{{ $product->title }}"
            >
                {{ $product->title }}
            </h3>
            <div>
                <span class="text-2xl font-semibold">
                    {{ $latestPrice->getPriceFormatted() }}
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-bold display-block">
                    {{ '@'.$latestPrice->getStoreName() }}
                </span>
                @if (! $product->is_last_scrape_successful)
                    <div class="mt-1">
                        @include('components.icon-badge', [
                            'hoverText' => __('One or more urls failed last scrape'),
                            'label' => __('Scrape error'),
                            'color' => 'warning',
                        ])
                    </div>
                @endif

                @if ($product->is_notified_price)
                    <div class="mt-1">
                        @include('components.icon-badge', [
                        'hoverText' => __('Price matches your target'),
                        'label' => __('Notify match'),
                        'color' => 'success',
                        'icon' => 'heroicon-m-shopping-bag'
                    ])
                    </div>
                @endif
            </div>
        </div>
    </a>

    <x-product-card-detail :product="$product" />

</div>
