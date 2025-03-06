@php
    $maxHeight = $maxHeight ?? null;
@endphp
<div>
    <div
        style="height: {{ $height }}"
        id="{{ uniqid('chart-wrapper-'.$product->getKey().'-') }}"
        ax-load
        x-data="pbChart({
            cachedData: {{ json_encode($cachedDatasets) }},
            options: @js($options),
            type: @js($type),
        })"
    >
        <canvas
            x-ref="canvas"
            id="{{ uniqid('chart-canvas-'.$product->getKey().'-') }}"
            @if ($maxHeight)
                style="max-height: {{ $maxHeight }}"
            @endif
        ></canvas>
    </div>
</div>

