@php
    $maxHeight = $maxHeight ?? null;
@endphp
<div>
    <div
        style="height: {{ $height }}"
        ax-load
        x-data="pbChart({
            cachedData: {{ json_encode($cachedDatasets) }},
            options: @js($options),
            type: @js($type),
        })"
    >
        <canvas
            x-ref="canvas"
            @if ($maxHeight)
                style="max-height: {{ $maxHeight }}"
            @endif
        ></canvas>
    </div>
</div>

