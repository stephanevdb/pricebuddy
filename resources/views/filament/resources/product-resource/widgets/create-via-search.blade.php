<x-filament-widgets::widget>

    <h3 class="fi-header-heading my-6 text-2xl font-bold tracking-tight text-gray-950 dark:text-white" id="searchHeading">
        Or search for a product
    </h3>

    <x-filament-panels::form
        id="form"
        :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
        wire:submit="save"
    >
        {{ $this->form }}

    </x-filament-panels::form>

    <div class="min-h-96">
        <div wire:loading.delay.longer>

            <span class="flex items-center my-4 gap-2 py-2 px-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <x-filament::loading-indicator class="h-5 w-5"/><span>Searching...</span>
            </span>

        </div>

        @if ($searchQuery)
            <div wire:loading.delay.longer.class="opacity-10" class="mt-6" x-init="window.document.getElementById('searchHeading').scrollIntoView({behavior: 'smooth'})">
                @livewire(\App\Filament\Resources\ProductResource\Widgets\CreateViaSearchTable::class, ['searchQuery' => $searchQuery])
            </div>
        @endif
    </div>

</x-filament-widgets::widget>
