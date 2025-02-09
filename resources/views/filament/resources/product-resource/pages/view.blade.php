@php
    /** @var App\Models\Product $record */
    use App\Services\Helpers\IntegrationHelper;
    $searchEnabled = IntegrationHelper::isSearchEnabled();
@endphp
<x-filament-panels::page class="fi-dashboard-page product-view" xmlns:x-filament="http://www.w3.org/1999/html">

    <div x-data="{ tab: 'overview' }">
        {{-- Tabs--}}
        <x-filament::tabs label="Content tabs">
            <x-filament::tabs.item @click="tab = 'overview'" :alpine-active="'tab === \'overview\''">
                <div class="flex align-center gap-2">
                    <x-filament::icon icon="heroicon-m-rectangle-stack" class="w-4"/>
                    Overview
                </div>
            </x-filament::tabs.item>

            <x-filament::tabs.item @click="tab = 'history'" :alpine-active="'tab === \'history\''">
                <div class="flex align-center gap-2">
                    <x-filament::icon icon="heroicon-m-chart-bar" class="w-4"/>
                    History
                </div>
            </x-filament::tabs.item>

            @if ($searchEnabled)
                <x-filament::tabs.item @click="tab = 'search'" :alpine-active="'tab === \'search\''">
                    <div class="flex align-center gap-2">
                        <x-filament::icon icon="heroicon-m-magnifying-glass" class="w-4"/>
                        Web search
                    </div>
                </x-filament::tabs.item>
            @endif
        </x-filament::tabs>

        {{-- Tab content --}}
        <div class="mt-8">
            <div x-show="tab === 'overview'" x-init="console.log('I am called second')">
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
                        <div class="mt-4 md:mt-6">
                            <div class="pb-2 md:pb-4">
                                <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Created :date', ['date' => $record->created_at->diffForHumans()]) }}
                                    {{ $record->tags->count() > 0 ? __('in').':' : '' }}
                                </div>
                                <div class="flex gap-2 flex-wrap">
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
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col md:h-full mb-2">
                        <div>
                            @livewire(\App\Filament\Resources\ProductResource\Widgets\ProductUrlStats::class, ['record'
                            => $record])
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="tab === 'history'">
                @livewire(\App\Filament\Resources\ProductResource\Widgets\PriceHistoryChart::class, ['record' =>
                $record])
            </div>

            @if ($searchEnabled)
                <div x-show="tab === 'search'">
                    @livewire(\App\Filament\Resources\ProductResource\Widgets\ProductSearch::class, ['record' => $record])
                </div>
            @endif

        </div>
    </div>

</x-filament-panels::page>
