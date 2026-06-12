@props([
    'id' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'autofocus' => false,
    'icon' => null,
])

@php
    $hasError = $errors->has($name ?? $id ?? '');
    $inputClass = 'input-default' . ($hasError ? ' input-error' : '');
@endphp

<div class="relative">
    @if ($icon)
        <span class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
              style="color: {{ $hasError ? '#EF4444' : '#9CA3AF' }};">
            {{ $icon }}
        </span>
    @endif

    <input
        id="{{ $id ?? $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name ?? $id ?? '', $value) }}"
        placeholder="{{ $placeholder }}"
        class="{{ $inputClass }}"
        {{ $required ? 'required' : '' }}
        {{ $autofocus ? 'autofocus' : '' }}
        {{ $attributes }}
    />
</div>

@error($name ?? $id ?? '')
    <p class="mt-1.5 text-xs flex items-center gap-1" style="color: #EF4444;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" stroke="#EF4444" stroke-width="2"/>
            <path d="M12 8v4M12 16h.01" stroke="#EF4444" stroke-width="2" stroke-linecap="round"/>
        </svg>
        {{ $message }}
    </p>
@enderror
