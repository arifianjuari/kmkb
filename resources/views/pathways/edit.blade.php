@extends('layouts.app')

@section('content')
<section class="mx-auto py-6 sm:px-6 lg:px-8">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Edit Clinical Pathway') }}</h2>
            <a href="{{ route('pathways.index') }}" class="btn-secondary">
                {{ __('Back to List') }}
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('pathways.update', $pathway) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Name') }}</label>
                        <div class="mt-1">
                            <input type="text" id="name" name="name" value="{{ old('name', $pathway->name) }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 @error('name') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Description') }}</label>
                        <div class="mt-1">
                            <textarea id="description" name="description" rows="3" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 @error('description') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description', $pathway->description) }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label for="diagnosis_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Diagnosis Code') }}</label>
                            <div class="mt-1">
                                <input type="text" id="diagnosis_code" name="diagnosis_code" value="{{ old('diagnosis_code', $pathway->diagnosis_code) }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 @error('diagnosis_code') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            @error('diagnosis_code')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="version" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Version') }}</label>
                            <div class="mt-1">
                                <input type="text" id="version" name="version" value="{{ old('version', $pathway->version) }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 @error('version') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            @error('version')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2 mt-6">
                        <div>
                            <label for="effective_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Effective Date') }}</label>
                            <div class="mt-1">
                                <input type="date" id="effective_date" name="effective_date" value="{{ old('effective_date', $pathway->effective_date->format('Y-m-d')) }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 @error('effective_date') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            @error('effective_date')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                            <div class="mt-1">
                                <select id="status" name="status" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 @error('status') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">{{ __('Select Status') }}</option>
                                    <option value="draft" {{ old('status', $pathway->status) == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                    <option value="active" {{ old('status', $pathway->status) == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="inactive" {{ old('status', $pathway->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                </select>
                            </div>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="unit_cost_version" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Default Unit Cost Version') }}</label>
                        <div class="mt-1">
                            <select id="unit_cost_version" name="unit_cost_version" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 @error('unit_cost_version') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">{{ __('Pilih versi unit cost default') }}</option>
                                @foreach($versions ?? [] as $version)
                                    <option value="{{ $version }}" {{ old('unit_cost_version', $pathway->unit_cost_version) == $version ? 'selected' : '' }}>{{ $version }}</option>
                                @endforeach
                            </select>
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Versi unit cost yang akan digunakan sebagai default saat menambah step baru di pathway ini.') }}</p>
                        @error('unit_cost_version')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-2">
                        <a href="{{ route('pathways.index') }}" class="btn-secondary">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn-primary">
                            {{ __('Update Pathway') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
