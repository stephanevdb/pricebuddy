@php
    use function Filament\Support\get_color_css_variables;
    $hoverText = $hoverText ?? null;
    $label = $label ?? null;
    $color = $color ?? 'warning';
    $icon = $icon ?? 'heroicon-m-exclamation-triangle';
@endphp
<span
    title="{{ $hoverText }}"
    style="{{ get_color_css_variables(
        $color,
        shades: [400, 500],
    ) }}"
    class="text-custom-500 dark:text-custom-400 flex gap-2 items-center text-xs"
>
    <x-filament::icon
        icon="{{ $icon }}"
        class="text-custom-500 dark:text-custom-400 w-4 h-4"
    />
    @if($label)
        <span>{{ $label }}</span>
    @endif
</span>
