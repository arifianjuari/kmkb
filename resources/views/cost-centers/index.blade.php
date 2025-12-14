@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Cost Centers') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('cost-centers-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is Cost Center?') }}"
                    title="{{ __('What is Cost Center?') }}"
                >
                    i
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <form method="GET" action="{{ route('cost-centers.index') }}" class="flex items-center space-x-2">
                    <input type="hidden" name="view_mode" value="{{ $viewMode ?? 'tree' }}">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('Search by code, division, or building name...') }}" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                    <select name="type" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="support" {{ $type == 'support' ? 'selected' : '' }}>{{ __('Support') }}</option>
                        <option value="revenue" {{ $type == 'revenue' ? 'selected' : '' }}>{{ __('Revenue') }}</option>
                    </select>
                    <select name="is_active" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="1" {{ $isActive === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="0" {{ $isActive === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                        {{ __('Filter') }}
                    </button>
                    @if($search || $type || $division || $isActive !== null)
                        <a href="{{ route('cost-centers.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </form>
                <div class="flex items-center gap-2 border-l pl-2 ml-2">
                    <a href="{{ route('cost-centers.index', array_merge(request()->query(), ['view_mode' => 'tree'])) }}" class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium {{ ($viewMode ?? 'tree') === 'tree' ? 'bg-biru-dongker-800 text-white border-biru-dongker-800' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                        Tree
                    </a>
                    <a href="{{ route('cost-centers.index', array_merge(request()->query(), ['view_mode' => 'flat'])) }}" class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium {{ ($viewMode ?? 'tree') === 'flat' ? 'bg-biru-dongker-800 text-white border-biru-dongker-800' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        Flat
                    </a>
                </div>
                <a href="{{ route('cost-centers.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    {{ __('Export Excel') }}
                </a>
                @if(!auth()->user()?->isObserver())
                <a href="{{ route('cost-centers.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                    {{ __('Add New Cost Center') }}
                </a>
                @endif
            </div>
        </div>
        <div id="cost-centers-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Cost center</span> (pusat biaya) adalah unit organisasi di rumah sakit tempat biaya dikumpulkan dan dicatat. Fokusnya bukan siapa yang membayar, tetapi biaya itu lahir di unit mana.
            </p>
            <div class="mb-2">
                <p class="font-semibold mb-1">Klasifikasi utama:</p>
                <ol class="list-decimal list-inside space-y-2 ml-2">
                    <li>
                        <span class="font-semibold">Pusat biaya produksi (revenue / unit produksi)</span>
                        <ul class="list-disc list-inside ml-4 space-y-1">
                            <li>Unit yang langsung melayani pasien dan menghasilkan pendapatan</li>
                            <li>Contoh: rawat inap, rawat jalan, kamar operasi, ICU, laboratorium, radiologi</li>
                            <li>Biaya melekat: gaji perawat/DPJP di unit tersebut, obat & BHP pasien, alat medis, dll</li>
                            <li>Dari sini dihitung unit cost layanan (tarif dasar klinis)</li>
                        </ul>
                    </li>
                    <li>
                        <span class="font-semibold">Pusat biaya penunjang (support / overhead)</span>
                        <ul class="list-disc list-inside ml-4 space-y-1">
                            <li>Unit yang tidak langsung melayani pasien dan tidak menghasilkan pendapatan, tetapi menopang unit produksi</li>
                            <li>Contoh: direksi & manajemen, administrasi, keuangan, laundry, gizi, IPSRS, IT, keamanan, dll</li>
                            <li>Biaya (gaji manajemen, ATK, listrik kantor, biaya laundry, dll.) akan dialokasikan ke unit produksi sebagai overhead</li>
                        </ul>
                    </li>
                </ol>
            </div>
            <div>
                <p class="font-semibold mb-1">Konsekuensi di KMKB & webapp:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Setiap unit di RS harus diberi kode cost center</li>
                    <li>Biaya dikumpulkan per cost center → biaya unit penunjang dialokasikan ke unit produksi → total biaya unit produksi dipakai untuk menghitung unit cost per layanan dan dibandingkan dengan tarif (INA-CBG, tarif internal, dll.)</li>
                </ul>
            </div>
        </div>
        
        @if(session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @php
                    $typeTabs = [
                        'all' => __('All Types'),
                        'support' => __('Support'),
                        'revenue' => __('Revenue'),
                    ];
                @endphp

                <div class="mb-6 space-y-4">
                    {{-- Type Tabs (Indigo) --}}
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">{{ __('Type') }}</p>
                        <div class="flex flex-wrap items-center gap-1.5">
                            @foreach($typeTabs as $key => $label)
                                @php
                                    $isActiveTab = ($key === 'all' && !$type) || ($key === $type);
                                    $urlParams = request()->except('type', 'page');
                                    if ($key !== 'all') {
                                        $urlParams['type'] = $key;
                                    }
                                    $tabUrl = route('cost-centers.index', $urlParams);
                                @endphp
                                <a
                                    href="{{ $tabUrl }}"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 border rounded-full text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-biru-dongker-700 {{ $isActiveTab ? 'bg-biru-dongker-800 text-white border-biru-dongker-800' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}"
                                >
                                    <span>{{ $label }}</span>
                                    @if(isset($typeCounts))
                                        <span class="text-[10px] font-semibold {{ $isActiveTab ? 'text-white/80' : 'text-gray-500' }}">
                                            {{ $typeCounts[$key] ?? 0 }}
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Division Tabs --}}
                    @if(isset($divisions) && $divisions->count() > 0)
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">{{ __('Division') }}</p>
                        <div class="flex flex-wrap items-center gap-1.5">
                            @php
                                $isActiveDivisionTab = !$division;
                                $urlParams = request()->except('division', 'page');
                                $allDivisionTabUrl = route('cost-centers.index', $urlParams);
                            @endphp
                            <a
                                href="{{ $allDivisionTabUrl }}"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 border rounded-full text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-biru-dongker-700 {{ $isActiveDivisionTab ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}"
                            >
                                <span>{{ __('All Divisions') }}</span>
                                @if(isset($divisionCounts))
                                    <span class="text-[10px] font-semibold {{ $isActiveDivisionTab ? 'text-white/80' : 'text-gray-500' }}">
                                        {{ $divisionCounts['all'] ?? 0 }}
                                    </span>
                                @endif
                            </a>
                            @foreach($divisions as $div)
                                @php
                                    $isActiveDivisionTab = $division === $div->name;
                                    $urlParams = request()->except('division', 'page');
                                    $urlParams['division'] = $div->name;
                                    $divisionTabUrl = route('cost-centers.index', $urlParams);
                                @endphp
                                <a
                                    href="{{ $divisionTabUrl }}"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 border rounded-full text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-biru-dongker-700 {{ $isActiveDivisionTab ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}"
                                >
                                    <span>{{ $div->name }}</span>
                                    @if(isset($divisionCounts))
                                        <span class="text-[10px] font-semibold {{ $isActiveDivisionTab ? 'text-white/80' : 'text-gray-500' }}">
                                            {{ $divisionCounts[$div->name] ?? 0 }}
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                @if(isset($groupedByDivision) && ($viewMode ?? 'tree') === 'tree')
                    {{-- Tree View --}}
                    @if($groupedByDivision->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Cost Center') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Building Name') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Floor') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Class') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($groupedByDivision as $divisionName => $costCenters)
                                        @php
                                            $divisionId = 'division-' . md5($divisionName);
                                            $hasItems = $costCenters->count() > 0;
                                        @endphp
                                        <tr class="division-header-row bg-gray-100 hover:bg-gray-200" data-division="{{ $divisionName }}" data-division-id="{{ $divisionId }}">
                                            <td colspan="7" class="px-6 py-3">
                                                <div class="flex items-center">
                                                    <button type="button" 
                                                            class="division-toggle inline-flex items-center mr-2 cursor-pointer hover:bg-gray-300 rounded p-1 transition-colors" 
                                                            onclick="toggleDivision('{{ $divisionId }}', this)"
                                                            aria-label="Toggle division">
                                                        <svg class="w-4 h-4 text-gray-600 chevron-icon chevron-down" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                        <svg class="w-4 h-4 text-gray-600 chevron-icon chevron-right hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </button>
                                                    <span class="font-semibold text-gray-900">{{ $divisionName ?: __('No Division') }}</span>
                                                    <span class="ml-2 text-xs text-gray-500">({{ $costCenters->count() }})</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @foreach($costCenters->sortBy('building_name') as $costCenter)
                                            @include('cost-centers.partials.tree-row', ['costCenter' => $costCenter, 'divisionId' => $divisionId])
                                        @endforeach
                                    @endforeach
                                    @if($groupedByDivision->count() === 0)
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                {{ __('No cost centers found.') }}
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('No cost centers found.') }}</p>
                    @endif

                    @push('scripts')
                    <script>
                        function toggleDivision(divisionId, button) {
                            // Find all cost center rows that belong to this division
                            const costCenterRows = document.querySelectorAll(`tr.division-${divisionId}`);
                            
                            const chevronDown = button.querySelector('.chevron-down');
                            const chevronRight = button.querySelector('.chevron-right');
                            
                            // Check if currently expanded (first row is visible)
                            const isExpanded = costCenterRows.length > 0 && !costCenterRows[0].classList.contains('hidden');
                            
                            if (isExpanded) {
                                // Collapse: hide all cost center rows in this division
                                costCenterRows.forEach(row => {
                                    row.classList.add('hidden');
                                });
                                if (chevronDown) chevronDown.classList.add('hidden');
                                if (chevronRight) chevronRight.classList.remove('hidden');
                            } else {
                                // Expand: show all cost center rows in this division
                                costCenterRows.forEach(row => {
                                    row.classList.remove('hidden');
                                });
                                if (chevronDown) chevronDown.classList.remove('hidden');
                                if (chevronRight) chevronRight.classList.add('hidden');
                            }
                        }

                        // Initialize: all divisions are expanded by default
                        document.addEventListener('DOMContentLoaded', function() {
                            // All divisions are visible by default, so chevrons should show down arrow
                            // This is already the default state from the HTML
                        });
                    </script>
                    @endpush
                @else
                    {{-- Flat View --}}
                    @if(isset($costCenters) && $costCenters->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Cost Center') }}</th>
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Building Name') }}</th>
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Floor') }}</th>
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Class') }}</th>
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type') }}</th>
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Parent') }}</th>
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($costCenters as $costCenter)
                                        <tr>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $costCenter->name }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $costCenter->building_name ?? '-' }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $costCenter->floor ?? '-' }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $costCenter->tariffClass ? $costCenter->tariffClass->name : '-' }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $costCenter->type == 'support' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ $costCenter->type == 'support' ? __('Support') : __('Revenue') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $costCenter->parent ? $costCenter->parent->name : '-' }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $costCenter->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $costCenter->is_active ? __('Active') : __('Inactive') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('cost-centers.show', $costCenter) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('View') }}" aria-label="{{ __('View') }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                            <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                                                        </svg>
                                                    </a>
                                                    @if(!auth()->user()?->isObserver())
                                                    <a href="{{ route('cost-centers.edit', $costCenter) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-biru-dongker-800 hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('cost-centers.destroy', $costCenter) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this cost center?') }}')">
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
                            {{ $costCenters->links() }}
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('No cost centers found.') }}</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

