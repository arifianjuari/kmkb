@props([
    'name' => null,
    'logoUrl' => null,
    'color' => null,
    'compact' => false,
])

@php
    $displayName = trim((string) $name);
    if ($displayName === '') {
        $displayName = config('app.name', 'KMKB');
    }
@endphp

<div {{ $attributes->class(['flex min-w-0 items-center', 'gap-3' => !$compact]) }}>
    <div class="relative h-8 w-8 flex-none overflow-hidden rounded-full" data-tenant-logo-container>
        <x-hospital-avatar
            :name="$displayName"
            :color="$color"
            size="8"
            class="absolute inset-0"
            data-tenant-logo-fallback
        />

        @if(filled($logoUrl))
            <img
                src="{{ $logoUrl }}"
                alt="Logo {{ $displayName }}"
                class="absolute inset-0 h-8 w-8 bg-gray-200 object-contain"
                loading="eager"
                onerror="this.remove()"
                data-tenant-logo
            />
        @endif
    </div>

    @unless($compact)
        <span
            class="hidden min-w-0 flex-1 truncate text-lg font-semibold leading-tight lg:block"
            title="{{ $displayName }}"
            data-tenant-name
        >{{ $displayName }}</span>
    @endunless
</div>
