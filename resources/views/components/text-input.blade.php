@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-biru-dongker-700 focus:ring-biru-dongker-700 rounded-md shadow-sm']) !!}>
