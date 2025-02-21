@php
    use App\Enums\Trend;
    use function Filament\Support\get_color_css_variables;

    /** @var \App\Models\Product $product */
    /** @var \App\Dto\PriceCacheDto $latestPrice */
    $latestPrice = $product->getPriceCache()->first();
@endphp
<div
    class="display-block rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
    x-data="{ expanded: false }"
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
                        @include('components.warning-badge', [
                            'hoverText' => __('One or more urls failed last scrape'),
                            'label' => __('Scrape error'),
                        ])
                    </div>
                @endif
            </div>
        </div>
    </a>

    <div
        class="mt-1 border-t border-t-gray-200 dark:border-t-gray-800 bg-gray-200/20 dark:bg-gray-950/20"
        style="border-radius: 0 0 .5rem .5rem"
    >
        <button
            class="py-2 bg-custom-400/10 hover:bg-custom-400/20 cursor-pointer display-block w-full transition-colors duration-300 ease-in-out"
            style="height: 60px;"
            @click="expanded = !expanded"
        >
            <x-range-chart :product="$product" height="50px"/>
        </button>
        <div x-show="expanded">
            <div class="py-2 px-4 border-t border-t-gray-200 dark:border-t-gray-800 bg-white dark:bg-gray-900">
                @include('components.prices-column', ['items' => $product->price_cache])
            </div>
            <div class="mt-1 py-2 px-4 gap-2 flex border-t border-t-gray-200 dark:border-t-gray-800">
                @foreach (['min', 'avg', 'max'] as $agg)
                    <div class="text-xs text-gray-500 dark:text-gray-400 pr-2">
                        {{ ucfirst($agg) }}: {{ $product->price_aggregates[$agg] }}
                    </div>
                @endforeach
                <x-filament::icon
                    :icon="Trend::getIcon($product->trend)"
                    class="ml-auto w-4 text-custom-600 dark:text-custom-400"
                    title="Price trending {{ $product->trend }}"
                />
            </div>
        </div>
    </div>

</div>
