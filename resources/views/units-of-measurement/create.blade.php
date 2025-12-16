@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Add New Unit of Measurement') }}</h2>
        <a href="{{ route('units-of-measurement.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            {{ __('Back to List') }}
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('units-of-measurement.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">{{ __('Code') }} <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <input type="text" id="code" name="code" value="{{ old('code') }}" required placeholder="e.g. m2, kg, tablet" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Kode unik untuk unit ini (huruf kecil, tanpa spasi)') }}</p>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }} <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Meter Persegi, Kilogram" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="symbol" class="block text-sm font-medium text-gray-700">{{ __('Symbol') }}</label>
                        <div class="mt-1">
                            <input type="text" id="symbol" name="symbol" value="{{ old('symbol') }}" placeholder="e.g. m², kg, tab" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            @error('symbol')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Simbol yang ditampilkan (bisa sama dengan kode)') }}</p>
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">{{ __('Category') }} <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <select id="category" name="category" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('category', 'count') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="context" class="block text-sm font-medium text-gray-700">{{ __('Context') }} <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <select id="context" name="context" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                @foreach($contexts as $key => $label)
                                    <option value="{{ $key }}" {{ old('context', 'both') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('context')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Tentukan di mana unit ini dapat digunakan') }}</p>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-biru-dongker-700 focus:ring-biru-dongker-700 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">{{ __('Active') }}</label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('units-of-measurement.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                        {{ __('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
