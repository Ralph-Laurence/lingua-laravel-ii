{{-- dd(get_defined_vars()) --}}
@if ($attributes->has('action'))
    <a href="{{ $action }}"
@else
    <button type="{{ $type }}"
@endif

    class="{{ $buttonClass }}"
    {{ $attributes->except(['type', 'id', 'class', 'style', 'text']) }}
>
    @if (!empty($icon))
        <i class="fas {{ $icon }} me-1"></i>
    @endif
    {{ $text }}
@if ($attributes->has('action'))
    </a>
@else
    </button>
@endif
