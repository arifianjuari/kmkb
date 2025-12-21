@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 sm:flex sm:items-center sm:justify-between dark:border-gray-700">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Dashboard Summary Report') }}</h2>
            <a href="{{ route('reports.index') }}" class="btn-secondary">
                {{ __('Back to Reports') }}
            </a>
        </div>
        
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="bg-gray-50 rounded-lg p-6 dark:bg-gray-700">
                <h3 class="text-lg font-medium text-gray-900 mb-4 dark:text-white">{{ __('Report Filters') }}</h3>
                <form method="GET" action="{{ route('reports.dashboard') }}" class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date From') }}</label>
                        <input type="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-600 dark:border-gray-500 dark:text-white" id="date_from" name="date_from" value="{{ request('date_from', now()->subMonth()->format('Y-m-d')) }}">
                    </div>
                    
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date To') }}</label>
                        <input type="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-600 dark:border-gray-500 dark:text-white" id="date_to" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}">
                    </div>
                    
                    <div>
                        <label for="pathway_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Pathway') }}</label>
                        <select class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-600 dark:border-gray-500 dark:text-white" id="pathway_id" name="pathway_id">
                            <option value="">{{ __('All Pathways') }}</option>
                            @foreach($pathways as $pathway)
                                <option value="{{ $pathway->id }}" {{ request('pathway_id') == $pathway->id ? 'selected' : '' }}>
                                    {{ $pathway->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3 sm:col-span-3">
                        <button type="submit" class="btn-primary">
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('reports.dashboard') }}" class="btn-secondary">
                            {{ __('Clear') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                <div class="bg-blue-600 rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-blue-100 truncate">{{ __('Total Cases') }}</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-white">{{ number_format($totalCases) }}</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-600 rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-green-100 truncate">{{ __('Avg. Compliance') }}</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-white">{{ number_format($averageCompliance, 2) }}%</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-biru-dongker-800 rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-biru-dongker-700 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-biru-dongker-300 truncate">{{ __('Total Charges') }}</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-white">Rp{{ number_format($totalCharges, 2) }}</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-500 rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-400 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-yellow-100 truncate">{{ __('Total Variance') }}</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-white">Rp{{ number_format($totalCostVariance, 2) }}</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">{{ __('Cases by Pathway') }}</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        @if($casesByPathway->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Pathway') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Cases') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Avg. Compliance') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                        @foreach($casesByPathway as $data)
                                            <tr>
                                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $data->pathway_name }}</td>
                                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $data->case_count }}</td>
                                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ number_format($data->avg_compliance, 2) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-600 dark:text-gray-400">{{ __('No data available.') }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">{{ __('Monthly Trend') }}</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        @if($monthlyTrend->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Month') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Cases') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Avg. Compliance') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                        @foreach($monthlyTrend as $data)
                                            <tr>
                                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $data->month }}</td>
                                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $data->case_count }}</td>
                                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ number_format($data->avg_compliance, 2) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-600 dark:text-gray-400">{{ __('No data available.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
