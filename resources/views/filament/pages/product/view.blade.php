@php /** @var App\Models\Product $record */ @endphp
<x-filament-panels::page class="fi-dashboard-page product-view" xmlns:x-filament="http://www.w3.org/1999/html">
    <div class="flex gap-6 md:gap-8 flex-col md:flex-row">

        <div class="md:w-1/3 flex items-start flex items-center justify-center bg-white rounded-lg p-4 max-h-78">
            <div class="">
                <img
                    src="{{ $record->primary_image }}"
                    alt="{{ $record->title }}"
                    class="rounded-lg h-full block max-h-72"
                />
            </div>
        </div>

        <div class="flex-1 flex flex-col md:h-full mb-2">

            <div>
                @livewire(\App\Filament\Resources\ProductResource\Widgets\ProductUrlStats::class, ['record' => $record])
            </div>

            <div class="flex justify-start flex flex-col gap-4 pb-6 md:pb-8 mt-6">

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

            </div>

        </div>

    </div>
</x-filament-panels::page>
