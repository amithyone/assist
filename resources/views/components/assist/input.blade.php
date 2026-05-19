@props(['label', 'name', 'type' => 'text', 'required' => false])
<div class="assist-field">
    <label for="{{ $name }}">{{ $label }}</label>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $attributes->get('value')) }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'assist-input']) }}
    >
    @error($name)
        <p class="assist-error">{{ $message }}</p>
    @enderror
</div>
