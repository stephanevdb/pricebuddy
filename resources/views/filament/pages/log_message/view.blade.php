<x-filament-panels::page class="fi-dashboard-page" xmlns:x-filament="http://www.w3.org/1999/html">
    @foreach(data_get($record, 'context', []) as $key => $val)
        <div class="mb-2">
            <x-filament::section :heading="$key">
                <code class="block whitespace-pre overflow-x-auto">{{ is_string($val) ? $val : json_encode($val, JSON_PRETTY_PRINT) }}</code>
            </x-filament::section>
        </div>
    @endforeach
</x-filament-panels::page>


