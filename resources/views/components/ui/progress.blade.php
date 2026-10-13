@props(['value' => 0, 'label' => null])

@php
    $percent = max(0, min(100, (int) $value));
@endphp

<div class="grid gap-1.5">
    @if ($label)
        <div class="flex items-center justify-between text-sm">
            <span class="text-muted-foreground">{{ $label }}</span>
            <span class="tabular-nums font-medium">{{ $percent }}%</span>
        </div>
    @endif

    <div
        class="progress"
        role="progressbar"
        aria-valuenow="{{ $percent }}"
        aria-valuemin="0"
        aria-valuemax="100"
        @if ($label) aria-label="{{ $label }}" @endif
    >
        <span style="width: {{ $percent }}%"></span>
    </div>
</div>
