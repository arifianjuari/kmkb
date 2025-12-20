@extends('layouts.app')

@section('content')
@php
    $contextTabs = [
        'all' => __('All'),
        'allocation' => __('Allocation'),
        'service' => __('Service'),
        'both' => __('Both'),
    ];
@endphp
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Units of Measurement') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('uom-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is Unit of Measurement?') }}"
                    title="{{ __('What is Unit of Measurement?') }}"
                >
                    i
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <form method="GET" action="{{ route('units-of-measurement.index') }}" class="flex items-center space-x-2">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('Search...') }}" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                    <select name="category" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ $category == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                        {{ __('Filter') }}
                    </button>
                    @if($search || $category || $context)
                        <a href="{{ route('units-of-measurement.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </form>
                <a href="{{ route('units-of-measurement.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    {{ __('Export Excel') }}
                </a>
                @if(!auth()->user()?->isObserver())
                <button
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'import-uom-modal')"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                >
                    {{ __('Import Excel') }}
                </button>
                <a href="{{ route('units-of-measurement.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                    {{ __('Add New Unit') }}
                </a>
                @endif
            </div>
        </div>
        <div id="uom-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Unit of Measurement (UoM)</span> adalah daftar satuan pengukuran standar yang digunakan di seluruh aplikasi. UoM digunakan untuk:
            </p>
            <ul class="list-disc list-inside space-y-1">
                <li><strong>Allocation:</strong> Satuan untuk driver alokasi biaya (m², kg, orang, dll.)</li>
                <li><strong>Service:</strong> Satuan untuk layanan dan item di Cost References (tindakan, test, tablet, dll.)</li>
                <li><strong>Both:</strong> Satuan yang dapat digunakan untuk keduanya</li>
            </ul>
        </div>
        


        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="mb-6">
                    {{-- Context Tabs --}}
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider">{{ __('Context') }}</p>
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($contextTabs as $key => $label)
                                @php
                                    $isActive = ($key === 'all' && !$context) || ($context === $key);
                                    $urlParams = request()->except('context', 'page');
                                    if ($key !== 'all') {
                                        $urlParams['context'] = $key;
                                    }
                                    $tabUrl = route('units-of-measurement.index', $urlParams);
                                @endphp
                                <a
                                    href="{{ $tabUrl }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 border rounded-full text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-biru-dongker-700 {{ $isActive ? 'bg-biru-dongker-800 text-white border-biru-dongker-800' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}"
                                >
                                    <span>{{ $label }}</span>
                                    <span class="text-xs font-semibold {{ $isActive ? 'text-white/80' : 'text-gray-500' }}">
                                        {{ $contextCounts[$key] ?? 0 }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($units->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Code') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Symbol') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Category') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Context') }}</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Active') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($units as $unit)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $unit->code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $unit->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $unit->symbol ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $categories[$unit->category] ?? $unit->category }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $contextColors = [
                                                    'allocation' => 'bg-blue-100 text-blue-800',
                                                    'service' => 'bg-green-100 text-green-800',
                                                    'both' => 'bg-purple-100 text-purple-800',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $contextColors[$unit->context] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $contexts[$unit->context] ?? $unit->context }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            @if($unit->is_active)
                                                <span class="text-green-600 font-semibold">[✓]</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('units-of-measurement.show', $unit) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('View') }}" aria-label="{{ __('View') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                                                    </svg>
                                                </a>
                                                @if(!auth()->user()?->isObserver())
                                                <a href="{{ route('units-of-measurement.edit', $unit) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-biru-dongker-800 hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('units-of-measurement.destroy', $unit) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this unit of measurement?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                            <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1Zm-3 6h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 9Zm3 2v8h2v-8H9Zm4 0v8h2v-8h-2Z"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $units->links() }}
                    </div>
                @else
                    <p class="text-gray-600">{{ __('No units of measurement found.') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <x-modal name="import-uom-modal" :show="false" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Import Units of Measurement') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Upload an Excel file to import units of measurement. Existing records with the same Code will be updated.') }}
            </p>

            <div class="mt-4">
                <a href="{{ route('units-of-measurement.template') }}" class="text-sm text-blue-600 hover:text-blue-900 underline">
                    {{ __('Download Template') }}
                </a>
            </div>

            <form method="POST" action="{{ route('units-of-measurement.import') }}" enctype="multipart/form-data" class="mt-6">
                @csrf
                
                <div>
                    <x-input-label for="file" :value="__('Excel File')" />
                    <input id="file" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="file" accept=".xlsx, .xls" required>
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button class="ml-3">
                        {{ __('Import') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
@endsection
