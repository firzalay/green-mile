@props([
    'type' => 'submit',
    'fullWidth' => false,
])

<button
    type="{{ $type }}"
    class="btn-primary {{ $fullWidth ? 'w-full' : '' }}"
    {{ $attributes }}>
    {{ $slot }}
</button>
