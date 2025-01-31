@php /** @var App\Models\Product $record */ @endphp
<x-filament-panels::page class="fi-dashboard-page product-view" xmlns:x-filament="http://www.w3.org/1999/html">
    <div class="flex gap-3 md:gap-8 flex-col md:flex-row">

        <div class="md:w-1/3 flex flex-col">
            <div class="bg-white rounded-lg p-4 h-auto w-full flex justify-center">
                <div class="">
                    <img
                        src="{{ $record->primary_image }}"
                        alt="{{ $record->title }}"
                        class="rounded-lg h-auto w-full block max-h-72 md:max-h-96"
                    />
                </div>
            </div>
            <div class="mt-4 md:mt-6 flex justify-start gap-2 flex-wrap">
                @foreach($record->tags as $tag)
                    <x-filament::badge
                        tag="a"
                        color="gray"
                        class="mb-1"
                        icon="heroicon-s-tag"
                        href="/admin/products?tableFilters[tags][values][0]={{ $tag->id }}"
                    >{{ $tag->name }}</x-filament::badge>
                @endforeach
            </div>
        </div>


        <div class="flex-1 flex flex-col md:h-full mb-2">

            <div>
                @livewire(\App\Filament\Resources\ProductResource\Widgets\ProductUrlStats::class, ['record' => $record])
            </div>

            <div class="flex justify-between flex flex-col md:flex-row md:items-center gap-4 pb-6 md:pb-8 mt-6">

                <x-filament::modal width="5xl">
                    <x-slot name="heading">
                        Price history
                    </x-slot>
                    <x-slot name="trigger">
                        <x-filament::button icon="heroicon-m-chart-bar" color="gray">
                            Detailed price history
                        </x-filament::button>
                    </x-slot>

                    @livewire(\App\Filament\Resources\ProductResource\Widgets\PriceHistoryChart::class, ['record' => $record])
                </x-filament::modal>

                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Created :date', ['date' => $record->created_at->diffForHumans()]) }}
                </div>
            </div>

        </div>

    </div>
</x-filament-panels::page>
