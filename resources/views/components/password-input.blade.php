@props([
    'id' => null,
    'name' => 'password',
    'placeholder' => 'Enter your password',
    'required' => false,
])

@php
    $hasError = $errors->has($name ?? $id ?? '');
    $inputClass = 'input-default' . ($hasError ? ' input-error' : '');
    $fieldId = $id ?? $name;
@endphp

<div class="relative">
    {{-- Lock icon prefix --}}
    <span class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
          style="color: {{ $hasError ? '#EF4444' : '#9CA3AF' }};">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </span>

    <input
        id="{{ $fieldId }}"
        name="{{ $name }}"
        type="password"
        placeholder="{{ $placeholder }}"
        class="{{ $inputClass }}"
        style="padding-right: 2.75rem;"
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    />

    {{-- Toggle show/hide password --}}
    <button
        type="button"
        onclick="togglePassword('{{ $fieldId }}')"
        class="absolute right-3 top-1/2 -translate-y-1/2 focus:outline-none transition-opacity hover:opacity-70"
        style="color: #9CA3AF;"
        aria-label="Toggle password visibility"
        id="toggle-{{ $fieldId }}">
        {{-- Eye icon (show password) --}}
        <svg id="eye-open-{{ $fieldId }}" width="17" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
        </svg>
        {{-- Eye-off icon (hide password, hidden by default) --}}
        <svg id="eye-closed-{{ $fieldId }}" width="17" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none;">
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <line x1="1" y1="1" x2="23" y2="23" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </button>
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

<script>
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const eyeOpen = document.getElementById('eye-open-' + fieldId);
        const eyeClosed = document.getElementById('eye-closed-' + fieldId);

        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.style.display = 'none';
            eyeClosed.style.display = 'block';
        } else {
            input.type = 'password';
            eyeOpen.style.display = 'block';
            eyeClosed.style.display = 'none';
        }
    }
</script>
