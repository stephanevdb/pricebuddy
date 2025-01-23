@php
    use App\Enums\Icons;
    $items = $getState();
@endphp
@if ($items)
    <ul class="my-2 text-sm">
        @foreach($items as $price)
            <li style="{{ Filament\Support\get_color_css_variables($price['trend_color'], shades: [400, 600]) }}">

                <a href="{{ $price['url'] }}" target="_blank" class="flex gap-2 ">
                    <x-filament::icon :icon="Icons::getTrendIcon($price['trend'])" class="w-4 text-custom-600 dark:text-custom-400" />
                    {{ $price['price'] }}
                    ({{ $price['store_name'] }})
                </a>

            </li>
        @endforeach
    </ul>
@endif

