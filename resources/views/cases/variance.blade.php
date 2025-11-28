@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Case Variance Analysis') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('variance-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is Variance Analysis?') }}"
                    title="{{ __('What is Variance Analysis?') }}"
                >
                    i
                </button>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('cases.show', $case) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    {{ __('Back to Case') }}
                </a>
                <a href="{{ route('cases.costing', $case) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                    {{ __('View Costing') }}
                </a>
            </div>
        </div>

        <div id="variance-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Variance Analysis</span> membandingkan planned steps (dari pathway) dengan actual steps (yang dilakukan) serta analisis variance biaya. Berguna untuk identifikasi deviasi dari rencana dan area yang memerlukan perbaikan.
            </p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li><strong>Favorable Variance:</strong> Actual cost lebih rendah dari estimate (hijau)</li>
                <li><strong>Unfavorable Variance:</strong> Actual cost lebih tinggi dari estimate (merah)</li>
                <li><strong>Compliance Rate:</strong> Persentase planned steps yang dilakukan</li>
                <li><strong>Custom Steps:</strong> Services yang dilakukan tapi tidak ada di pathway</li>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Compliance Rate') }}</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($summary['compliance_rate'], 1) }}%</dd>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Favorable Variances') }}</dt>
                                <dd class="text-lg font-medium text-green-600">{{ number_format($summary['favorable_variances'], 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Unfavorable Variances') }}</dt>
                                <dd class="text-lg font-medium text-red-600">{{ number_format($summary['unfavorable_variances'], 0, ',', '.') }}</dd>
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
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step Compliance Analysis -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Step Compliance Analysis') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Step') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Category') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Estimated Cost') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actual Cost') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Variance') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($varianceByStep as $item)
                                <tr class="{{ !$item['performed'] ? 'bg-gray-50' : '' }}">
                                    <td class="px-6 py-4 text-sm">
                                        <div class="font-medium text-gray-900">{{ $item['step']->step_order ?? '-' }}</div>
                                        <div class="text-gray-500 text-xs">{{ Str::limit($item['step']->description ?? '-', 40) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item['step']->category ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @if($item['performed'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ __('Performed') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ __('Not Performed') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        Rp {{ number_format($item['estimated_cost'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ $item['performed'] ? 'Rp ' . number_format($item['actual_cost'], 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right {{ $item['status'] === 'favorable' ? 'text-green-600' : 'text-red-600' }}">
                                        @if($item['performed'])
                                            {{ $item['variance'] >= 0 ? '+' : '' }}Rp {{ number_format($item['variance'], 0, ',', '.') }}
                                            <span class="text-xs text-gray-500">({{ number_format($item['variance_percent'], 1) }}%)</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Custom Steps -->
        @if($customSteps->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Custom Steps (Not in Pathway)') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Quantity') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actual Cost') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customSteps as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $item['detail']->service_item ?? 'Custom Service' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ number_format($item['detail']->quantity ?? 1, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        Rp {{ number_format($item['actual_cost'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Cost Summary Comparison -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Cost Summary Comparison') }}</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">{{ __('Pathway Estimated Cost') }}</p>
                        <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($summary['pathway_estimated_cost'], 0, ',', '.') }}</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">{{ __('Actual Total Cost') }}</p>
                        <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($summary['actual_total_cost'], 0, ',', '.') }}</p>
                    </div>
                    <div class="text-center p-4 {{ $summary['cost_variance'] >= 0 ? 'bg-red-50' : 'bg-green-50' }} rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">{{ __('Cost Variance') }}</p>
                        <p class="text-lg font-semibold {{ $summary['cost_variance'] >= 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $summary['cost_variance'] >= 0 ? '+' : '' }}Rp {{ number_format($summary['cost_variance'], 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ number_format($summary['cost_variance_percent'], 1) }}%
                        </p>
                    </div>
                </div>
                
                @if($summary['ina_cbg_tariff'] > 0)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">{{ __('INA-CBG Comparison') }}</h4>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
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
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

