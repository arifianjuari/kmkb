@props([
    'name' => 'RS',
    'color' => null, // expects HEX like #2563eb; falls back to Tailwind class if null
    'size' => '10', // supported common sizes: 8, 9, 10, 12, 16, 20
    'rounded' => 'full', // full | md | lg | xl
    'class' => '',
])

@php
    $displayName = trim($name ?? '');
    if ($displayName === '') {
        $displayName = config('app.name', 'RS');
    }

    // Build initials: take first letter of up to 3 words (e.g., "RS Bina Bhakti" -> "RSB" or "RSBB" if desired).
    // We'll aim for up to 3 initials to capture common Indonesian hospital abbreviations better.
    $words = preg_split('/\s+/', $displayName) ?: [];
    $initials = '';
    foreach ($words as $w) {
        $ch = mb_substr($w, 0, 1, 'UTF-8');
        if ($ch !== '') {
            $initials .= mb_strtoupper($ch, 'UTF-8');
        }
        if (mb_strlen($initials, 'UTF-8') >= 3) {
            break;
        }
    }
    if ($initials === '') {
        $initials = 'RS';
    }

    // Size mapping to Tailwind classes (square avatars)
    $sizeMap = [
        '8' => 'h-8 w-8 text-xs',
        '9' => 'h-9 w-9 text-xs',
        '10' => 'h-10 w-10 text-sm',
        '12' => 'h-12 w-12 text-base',
        '16' => 'h-16 w-16 text-xl',
        '20' => 'h-20 w-20 text-2xl',
    ];
    $sizeKey = (string) $size;
    $sizeClass = $sizeMap[$sizeKey] ?? $sizeMap['10'];

    // Rounded mapping
    // Always use full rounded to ensure perfect circle
    $roundedClass = 'rounded-full';

    // Background styling: prefer inline HEX color if provided; otherwise use Tailwind biru-dongker.
    $style = '';
    $bgClass = 'bg-biru-dongker-800';
    if ($color && is_string($color)) {
        // sanitize: allow only valid hex color like #RRGGBB or #RGB
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color)) {
            $style = 'background-color: ' . $color . ';';
            $bgClass = '';
        }
    }
@endphp

<div {{ $attributes->merge(['class' => "inline-flex items-center justify-center flex-none shrink-0 $sizeClass aspect-square overflow-hidden $bgClass text-white font-semibold leading-none select-none $class rounded-full"]) }} @if($style) style="{{ $style }}" @endif>
    <span class="pointer-events-none">{{ $initials }}</span>
</div>
