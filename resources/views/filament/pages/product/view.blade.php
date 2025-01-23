@php /** @var App\Models\Product $record */ @endphp
<x-filament-panels::page class="fi-dashboard-page" xmlns:x-filament="http://www.w3.org/1999/html">
    <div class="flex gap-8 flex-col md:flex-row md:items-stretch">

        <div class="md:w-1/3 md:h-full flex md:items-stretch">
            <x-filament::card class="w-full justify-center md:h-full">
                <div class="flex items-center bg-white rounded-lg p-2 min-h-36 md:min-h-72">
                    <img
                        src="{{ $record->primary_image }}"
                        alt="{{ $record->title }}"
                        class="w-full rounded-lg"
                    />
                </div>
            </x-filament::card>
        </div>

        <div class="flex-1 flex flex-col gap-8 md:h-full">
            <x-filament::card class="w-full justify-center md:h-full flex md:items-stretch">
                <ul>
                    @foreach($record->price_cache as $idx => $price)
                        @if ($idx === 0)
                            <li class="flex items-center pb-4">
                                <strong class="text-3xl font-bold pe-4" style="color: rgb(var(--primary-400))">{{ $price['price'] }}</strong>
                                <a href="{{ $price['url'] }}" target="_blank" title="Product page" class="store-link">{{ '@'.$price['store_name'] }} (Best price)</a>
                                <hr />
                            </li>
                        @else
                            @if ($idx === 1)
                                <li class="flex pt-4 pb-2 border-t border-gray-200 dark:border-white/10">
                            @else
                                <li class="flex pb-2">
                            @endif
                                <strong class="text-l font-bold pe-4">{{ $price['price'] }}</strong>
                                <a href="{{ $price['url'] }}" target="_blank" title="Product page" class="store-link">{{ '@'.$price['store_name'] }}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
                <div class="flex justify-start flex gap-2 pt-8 mt-2 pb-2 border-t border-gray-200 dark:border-white/10">
                    <x-filament::button
                        tag="a"
                        icon="heroicon-m-pencil-square"
                        href="{{ $record->action_urls['edit'] }}"
                        color="gray"
                    >Edit</x-filament::button>
                    <form method="POST" action="{{ $record->action_urls['fetch'] }}" id="fetch_form">
                        @csrf
                        <x-filament::button
                            tag="button"
                            type="submit"
                            icon="heroicon-m-arrow-down-tray"
                            color="gray"
                            class="border-none"
                            x-init="
                                $el.addEventListener('click', () => {
                                    $el.innerHTML = 'Fetching...';
                                    $el.setAttribute('disabled', 'disabled')
                                    $el.closest('form').submit();
                                });"
                        >Fetch</x-filament::button>
                    </form>

                </div>
            </x-filament::card>
        </div>

    </div>
</x-filament-panels::page>
