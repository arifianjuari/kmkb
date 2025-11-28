{{-- 
    Component untuk check apakah user adalah observer (read-only)
    Usage: @include('components.observer-check')
    Setelah include ini, variabel $isObserver akan tersedia
--}}
@php
    $isObserver = auth()->user()?->isObserver() ?? false;
@endphp

