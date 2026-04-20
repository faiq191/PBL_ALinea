<button {{ $attributes->merge([
    'class' => 'flex items-center justify-center gap-2 py-2 rounded-lg text-sm transition'
]) }}>
    @isset($icon)
        <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
    @endisset

    {{ $slot }}
</button>
