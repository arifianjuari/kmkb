@extends('layouts.app')

@section('content')
<section class="mx-auto py-6 sm:px-6 lg:px-8">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Add JKN CBG Code') }}</h2>
            <a href="{{ route('jkn-cbg-codes.index') }}" class="btn-secondary">
                {{ __('Back to List') }}
            </a>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('jkn-cbg-codes.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('CBG Code') }}</label>
                            <div class="mt-1">
                                <input type="text" id="code" name="code" value="{{ old('code') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 @error('code') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            @error('code')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Name') }}</label>
                            <div class="mt-1">
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 @error('name') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 mt-6">
                        <div class="sm:col-span-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Description') }}</label>
                            <div class="mt-1">
                                <textarea id="description" name="description" rows="3" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 @error('description') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description') }}</textarea>
                            </div>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 mt-6">
                        <div class="sm:col-span-2">
                            <label for="service_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Service Type') }}</label>
                            <div class="mt-1">
                                <select id="service_type" name="service_type" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">{{ __('Select Service Type') }}</option>
                                    <option value="Rawat Inap" {{ old('service_type') == 'Rawat Inap' ? 'selected' : '' }}>{{ __('Inpatient') }}</option>
                                    <option value="Rawat Jalan" {{ old('service_type') == 'Rawat Jalan' ? 'selected' : '' }}>{{ __('Outpatient') }}</option>
                                </select>
                            </div>
                            @error('service_type')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="severity_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Severity Level') }}</label>
                            <div class="mt-1">
                                <select id="severity_level" name="severity_level" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">{{ __('Select Severity Level') }}</option>
                                    <option value="1" {{ old('severity_level') == '1' ? 'selected' : '' }}>1</option>
                                    <option value="2" {{ old('severity_level') == '2' ? 'selected' : '' }}>2</option>
                                    <option value="3" {{ old('severity_level') == '3' ? 'selected' : '' }}>3</option>
                                </select>
                            </div>
                            @error('severity_level')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="grouping_version" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Grouping Version') }}</label>
                            <div class="mt-1">
                                <input type="text" id="grouping_version" name="grouping_version" value="{{ old('grouping_version') }}" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 @error('grouping_version') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            @error('grouping_version')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 mt-6">
                        <div class="sm:col-span-3">
                            <label for="tariff" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Tariff') }}</label>
                            <div class="mt-1">
                                <input type="number" id="tariff" name="tariff" step="any" value="{{ old('tariff') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 @error('tariff') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            @error('tariff')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3 flex items-end">
                            <div class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input id="is_active" name="is_active" type="checkbox" class="h-4 w-4 text-biru-dongker-800 focus:ring-biru-dongker-700 border-gray-300 rounded" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                    {{ __('Active') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <a href="{{ route('jkn-cbg-codes.index') }}" class="btn-secondary">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn-primary">
                            {{ __('Save CBG Code') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
