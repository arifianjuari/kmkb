@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Unit Cost Results') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('unit-cost-results-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is Unit Cost Results?') }}"
                    title="{{ __('What is Unit Cost Results?') }}"
                >
                    i
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('costing-process.unit-cost.calculate') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                    {{ __('Calculate Unit Cost') }}
                </a>
            </div>
        </div>

        <div id="unit-cost-results-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Unit Cost Results</span> menampilkan hasil perhitungan biaya per layanan yang sudah dijalankan. Setiap hasil mencakup direct cost (material & labor), indirect cost (overhead dari allocation), dan total unit cost per layanan.
            </p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li>Filter berdasarkan versi, periode, atau cost center untuk melihat hasil spesifik</li>
                <li>Gunakan hasil ini sebagai dasar untuk simulasi tarif atau perbandingan versi</li>
                <li>Data dapat diekspor untuk analisis lebih lanjut</li>
            </ul>
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

        <!-- Filters -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('costing-process.unit-cost.results') }}" class="grid grid-cols-1 gap-4 md:grid-cols-6">
                    <div>
                        <label for="version_label" class="block text-sm font-medium text-gray-700">{{ __('Version') }}</label>
                        <select id="version_label" name="version_label" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                            <option value="">{{ __('All Versions') }}</option>
                            @foreach($availableVersions as $version)
                                <option value="{{ $version }}" {{ $versionLabel == $version ? 'selected' : '' }}>{{ $version }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="period_year" class="block text-sm font-medium text-gray-700">{{ __('Year') }}</label>
                        <select id="period_year" name="period_year" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                            <option value="">{{ __('All Years') }}</option>
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $periodYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div>
                        <label for="period_month" class="block text-sm font-medium text-gray-700">{{ __('Month') }}</label>
                        <select id="period_month" name="period_month" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                            <option value="">{{ __('All Months') }}</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $periodMonth == $m ? 'selected' : '' }}>
                                    {{ str_pad($m, 2, '0', STR_PAD_LEFT) }} - {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    
                    <div>
                        <label for="cost_center_id" class="block text-sm font-medium text-gray-700">{{ __('Cost Center') }}</label>
                        <select id="cost_center_id" name="cost_center_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                            <option value="">{{ __('All Cost Centers') }}</option>
                            @foreach($costCenters as $cc)
                                <option value="{{ $cc->id }}" {{ $costCenterId == $cc->id ? 'selected' : '' }}>
                                    {{ $cc->code }} - {{ $cc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">{{ __('Search') }}</label>
                        <input type="text" id="search" name="search" value="{{ $search }}" placeholder="{{ __('Service code/name...') }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                            {{ __('Filter') }}
                        </button>
                    </div>
                </form>
                
                @if($search || $versionLabel || $periodYear || $periodMonth || $costCenterId)
                    <div class="mt-4">
                        <a href="{{ route('costing-process.unit-cost.results') }}" class="text-sm text-biru-dongker-800 hover:text-biru-dongker-900">
                            {{ __('Clear Filters') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-4 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Total Records') }}</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($summaryStats['total_records'], 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Services') }}</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($summaryStats['total_services'], 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Avg Unit Cost') }}</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $summaryStats['avg_unit_cost'] ? 'Rp ' . number_format($summaryStats['avg_unit_cost'], 0, ',', '.') : '-' }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-biru-dongker-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Versions') }}</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($availableVersions->count(), 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($unitCosts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service Code') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service Description') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Cost Center') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Version') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Period') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Direct Material') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Direct Labor') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Overhead') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Unit Cost') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($unitCosts as $unitCost)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $unitCost->costReference ? $unitCost->costReference->service_code : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $unitCost->costReference ? $unitCost->costReference->service_description : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $unitCost->costReference && $unitCost->costReference->costCenter ? $unitCost->costReference->costCenter->name : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-biru-dongker-300 text-biru-dongker-900">
                                                {{ $unitCost->version_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ str_pad($unitCost->period_month, 2, '0', STR_PAD_LEFT) }}/{{ $unitCost->period_year }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            {{ number_format($unitCost->direct_cost_material, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            {{ number_format($unitCost->direct_cost_labor, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            {{ number_format($unitCost->indirect_cost_overhead, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
                                            {{ number_format($unitCost->total_unit_cost, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $unitCosts->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No unit cost results found') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('Get started by calculating unit cost for a period.') }}
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('costing-process.unit-cost.calculate') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                                {{ __('Calculate Unit Cost') }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
