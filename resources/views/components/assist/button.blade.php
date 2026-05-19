@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
])

@php
    $class = 'assist-btn assist-btn-' . $variant;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</button>
@endif
