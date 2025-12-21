@extends('layouts.app')

@section('content')
<style>
    .tooltip-container {
        position: relative;
        display: inline-block;
    }
    .tooltip-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        margin-left: 6px;
        border-radius: 50%;
        background-color: #e0e7ff;
        color: #4f46e5;
        font-size: 10px;
        font-weight: 600;
        cursor: help;
        transition: all 0.2s;
    }
    .tooltip-icon:hover {
        background-color: #4f46e5;
        color: #fff;
    }
    .tooltip-text {
        visibility: hidden;
        opacity: 0;
        width: 280px;
        background-color: #1f2937;
        color: #fff;
        text-align: left;
        border-radius: 8px;
        padding: 12px;
        position: absolute;
        z-index: 1000;
        bottom: 125%;
        left: 50%;
        margin-left: -140px;
        font-size: 13px;
        line-height: 1.5;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: opacity 0.3s, visibility 0.3s;
    }
    .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #1f2937 transparent transparent transparent;
    }
    .tooltip-container:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }
</style>
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Mapping Validation') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('mapping-validation-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is Mapping Validation?') }}"
                    title="{{ __('What is Mapping Validation?') }}"
                >
                    i
                </button>
            </div>
        </div>

        <div id="mapping-validation-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Mapping Validation</span> memeriksa kesiapan master data sebelum alokasi: apakah cost center support sudah dimapping, apakah revenue center punya katalog layanan, apakah cost reference memiliki pasangan cost center & expense category, serta apakah driver yang dipakai step allocation punya data statistik.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                <div>
                    <p class="font-semibold mb-1">Cost Center Mapping</p>
                    <ul class="list-disc list-inside space-y-1 ml-2">
                        <li>Support center → Allocation map</li>
                        <li>Revenue center → Service catalog</li>
                    </ul>
                </div>
                <div>
                    <p class="font-semibold mb-1">Expense Category Mapping</p>
                    <ul class="list-disc list-inside space-y-1 ml-2">
                        <li>Cost reference harus punya cost center</li>
                        <li>Cost reference harus punya expense category</li>
                    </ul>
                </div>
                <div>
                    <p class="font-semibold mb-1">Allocation Driver Mapping</p>
                    <ul class="list-disc list-inside space-y-1 ml-2">
                        <li>Driver dipakai di allocation map</li>
                        <li>Driver punya data statistik di periode {{ $month }}/{{ $year }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('costing-process.pre-allocation-check.mapping-validation') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700">{{ __('Year') }}</label>
                        <select id="year" name="year" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700">{{ __('Month') }}</label>
                        <select id="month" name="month" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                            {{ __('Check') }}
                        </button>
                    </div>
                    <div class="flex items-end">
                        <a href="{{ route('allocation-maps.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Go to Allocation Maps') }}
                        </a>
                    </div>
                </form>
                @if($availableDriverPeriods->count() > 0)
                    <div class="mt-4 text-xs text-gray-500">
                        <span class="font-semibold mr-2">{{ __('Periode driver statistic tersedia:') }}</span>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($availableDriverPeriods as $period)
                                <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                    {{ str_pad($period->period_month, 2, '0', STR_PAD_LEFT) }}/{{ $period->period_year }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-4 mb-6">
            <div class="bg-white overflow-visible shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    {{ __('Support CC without allocation map') }}
                                    <div class="tooltip-container">
                                        <span class="tooltip-icon">i</span>
                                        <span class="tooltip-text">Support cost center yang belum memiliki allocation map sebagai sumber step-down.</span>
                                    </div>
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($summaryStats['support_without_allocation'] ?? 0, 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-visible shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 18a9 9 0 110-18 9 9 0 010 18z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    {{ __('Revenue CC without services') }}
                                    <div class="tooltip-container">
                                        <span class="tooltip-icon">i</span>
                                        <span class="tooltip-text">Revenue cost center tanpa cost reference aktif. Layanan yang belum dipetakan tidak dapat dihitung unit cost-nya.</span>
                                    </div>
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($summaryStats['revenue_without_services'] ?? 0, 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-visible shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    {{ __('Unmapped Cost References') }}
                                    <div class="tooltip-container">
                                        <span class="tooltip-icon">i</span>
                                        <span class="tooltip-text">Katalog layanan tanpa cost center atau expense category. Data ini tidak bisa dihubungkan dengan GL/Unit Cost.</span>
                                    </div>
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($summaryStats['unmapped_cost_references'] ?? 0, 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-visible shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 {{ ($summaryStats['allocation_driver_issues'] ?? 0) > 0 ? 'text-red-400' : 'text-green-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    {{ __('Allocation Driver Issues') }}
                                    <div class="tooltip-container">
                                        <span class="tooltip-icon">i</span>
                                        <span class="tooltip-text">Driver yang belum dipakai allocation map atau belum punya statistik pada periode {{ str_pad($month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}.</span>
                                    </div>
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($summaryStats['allocation_driver_issues'] ?? 0, 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Support Cost Centers tanpa Allocation Map') }}</h3>
                    <a href="{{ route('allocation-maps.create') }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                        {{ __('Create Allocation Map') }}
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Code') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($supportCentersWithoutAllocation as $center)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{{ $center->code }}</td>
                                    <td class="px-6 py-2 text-sm text-gray-900">{{ $center->name }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm">
                                        <a href="{{ route('cost-centers.edit', $center) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-biru-dongker-800 hover:bg-biru-dongker-200">
                                            {{ __('Edit Cost Center') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-2 text-sm text-gray-500 text-center">{{ __('All support cost centers already mapped.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Revenue Cost Centers tanpa Service Catalog') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Code') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($revenueCentersWithoutServices as $center)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{{ $center->code }}</td>
                                    <td class="px-6 py-2 text-sm text-gray-900">{{ $center->name }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm">
                                        <a href="{{ route('cost-centers.show', $center) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-biru-dongker-800 hover:bg-biru-dongker-200">
                                            {{ __('View Detail') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-2 text-sm text-gray-500 text-center">{{ __('All revenue cost centers already have services.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Cost References tanpa Mapping Lengkap') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service Code') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Description') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($unmappedCostReferences as $reference)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{{ $reference->service_code }}</td>
                                    <td class="px-6 py-2 text-sm text-gray-900">{{ $reference->service_description }}</td>
                                    <td class="px-6 py-2 text-sm">
                                        <div class="space-y-1">
                                            @if(!$reference->cost_center_id)
                                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">{{ __('Missing Cost Center') }}</span>
                                            @endif
                                            @if(!$reference->expense_category_id)
                                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">{{ __('Missing Expense Category') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm">
                                        <a href="{{ route('cost-references.edit', $reference) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-biru-dongker-800 hover:bg-biru-dongker-200">
                                            {{ __('Fix Mapping') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-2 text-sm text-gray-500 text-center">{{ __('All cost references already mapped.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mb-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Allocation Drivers not used in maps') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Driver') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Unit') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($unusedDrivers as $driver)
                                    <tr>
                                        <td class="px-6 py-2 text-sm text-gray-900">{{ $driver->name }}</td>
                                        <td class="px-6 py-2 text-sm text-gray-900">{{ $driver->unit_measurement ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-2 text-sm text-gray-500 text-center">{{ __('All drivers are used.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Allocation Maps tanpa driver statistic periode ini') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Step') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Source Cost Center') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Driver') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($mapsMissingDriverStats as $map)
                                    <tr>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{{ $map->step_sequence }}</td>
                                        <td class="px-6 py-2 text-sm text-gray-900">{{ $map->sourceCostCenter?->name }} <span class="text-gray-500 text-xs">({{ $map->sourceCostCenter?->code }})</span></td>
                                        <td class="px-6 py-2 text-sm text-gray-900">{{ $map->allocationDriver?->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-2 text-sm text-gray-500 text-center">{{ __('All allocation maps have driver statistics for this period.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
