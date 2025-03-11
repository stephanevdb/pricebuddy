@php
    use App\Enums\Trend;
@endphp

<div
    class="product-card-column w-full" x-data="{ expanded: false }"
    style="{{ Filament\Support\get_color_css_variables(Trend::getColor($product->trend), shades: [300, 500, 400, 600, 800]) }}"
>
    <x-product-card-detail :product="$product" :standalone="$standalone" class="rounded-bl-xl ">
        <div class="bg-custom-400/10 hover:bg-custom-400/20 rounded-lg" style="height: 40px" :class="expanded ? 'rounded-b-none' : ''">
            <x-range-chart :product="$product" height="40px" class="rounded-lg" />
        </div>
    </x-product-card-detail>
</div>
