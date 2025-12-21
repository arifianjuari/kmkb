@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Case Costing') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('costing-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is Case Costing?') }}"
                    title="{{ __('What is Case Costing?') }}"
                >
                    i
                </button>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('cases.show', $case) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    {{ __('Back to Case') }}
                </a>
                <a href="{{ route('cases.variance', $case) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                    {{ __('View Variance Analysis') }}
                </a>
            </div>
        </div>

        <div id="costing-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Case Costing</span> menampilkan breakdown biaya kasus pasien dan perbandingan dengan estimasi pathway. Berguna untuk analisis biaya aktual vs estimasi dan identifikasi area yang memerlukan perhatian.
            </p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li><strong>Actual Cost:</strong> Total biaya aktual dari services yang dilakukan</li>
                <li><strong>Pathway Estimate:</strong> Estimasi biaya berdasarkan clinical pathway</li>
                <li><strong>Variance:</strong> Selisih antara actual dan estimate (positif = over budget, negatif = under budget)</li>
                <li><strong>INA-CBG Tariff:</strong> Tarif INA-CBG untuk perbandingan</li>
            </ul>
        </div>

        <!-- Case Information -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Case Information') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Medical Record Number') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $case->medical_record_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Clinical Pathway') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $case->clinicalPathway->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Primary Diagnosis') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $case->primary_diagnosis }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-4 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Actual Total Cost') }}</dt>
                                <dd class="text-lg font-medium text-gray-900">Rp {{ number_format($summary['actual_total_cost'], 0, ',', '.') }}</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Pathway Estimate') }}</dt>
                                <dd class="text-lg font-medium text-gray-900">Rp {{ number_format($summary['pathway_estimated_cost'], 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 {{ $summary['cost_variance'] >= 0 ? 'text-red-400' : 'text-green-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Cost Variance') }}</dt>
                                <dd class="text-lg font-medium {{ $summary['cost_variance'] >= 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $summary['cost_variance'] >= 0 ? '+' : '' }}Rp {{ number_format($summary['cost_variance'], 0, ',', '.') }}
                                    <span class="text-xs">({{ number_format($summary['cost_variance_percent'], 1) }}%)</span>
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
                            <svg class="h-6 w-6 text-purple-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Total Services') }}</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($summary['total_services'], 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cost Breakdown by Category -->
        @if($costByCategory->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Cost Breakdown by Category') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Category') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Services') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Cost') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Percentage') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($costByCategory as $item)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['category'] }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item['count'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($item['total_cost'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ $summary['actual_total_cost'] > 0 ? number_format(($item['total_cost'] / $summary['actual_total_cost']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Unit Cost vs Actual Cost Comparison -->
        @if($unitCostComparison->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Unit Cost vs Actual Cost Comparison') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Quantity') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Unit Cost') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actual Cost') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Unit Cost Total') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actual Total') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Variance') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($unitCostComparison as $item)
                                <tr>
                                    <td class="px-6 py-2 text-sm text-gray-900">{{ Str::limit($item['service'], 50) }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item['quantity'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($item['unit_cost'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($item['actual_cost'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($item['unit_cost_total'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($item['actual_cost_total'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-semibold text-right {{ $item['variance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $item['variance'] >= 0 ? '+' : '' }}Rp {{ number_format($item['variance'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- INA-CBG Comparison -->
        @if($summary['ina_cbg_tariff'] > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('INA-CBG Tariff Comparison') }}</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">{{ __('INA-CBG Tariff') }}</p>
                        <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($summary['ina_cbg_tariff'], 0, ',', '.') }}</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">{{ __('Actual Cost') }}</p>
                        <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($summary['actual_total_cost'], 0, ',', '.') }}</p>
                    </div>
                    <div class="text-center p-4 {{ $summary['ina_cbg_variance'] >= 0 ? 'bg-green-50' : 'bg-red-50' }} rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">{{ __('Variance') }}</p>
                        <p class="text-lg font-semibold {{ $summary['ina_cbg_variance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $summary['ina_cbg_variance'] >= 0 ? '+' : '' }}Rp {{ number_format($summary['ina_cbg_variance'], 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $summary['ina_cbg_tariff'] > 0 ? number_format(($summary['ina_cbg_variance'] / $summary['ina_cbg_tariff']) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
