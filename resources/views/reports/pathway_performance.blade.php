@extends('layouts.app')

@section('content')
<div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Pathway Performance Report') }}</h2>
            <a href="{{ route('reports.index') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0">
                {{ __('Back to Reports') }}
            </a>
        </div>
        
        <div class="p-6 border-b border-gray-200">
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Report Filters') }}</h3>
                <form method="GET" action="{{ route('reports.pathway-performance') }}" class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700">{{ __('Date From') }}</label>
                        <input type="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="date_from" name="date_from" value="{{ request('date_from', now()->subMonth()->format('Y-m-d')) }}">
                    </div>
                    
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700">{{ __('Date To') }}</label>
                        <input type="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="date_to" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}">
                    </div>
                    
                    <div>
                        <label for="pathway_id" class="block text-sm font-medium text-gray-700">{{ __('Pathway') }}</label>
                        <select class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="pathway_id" name="pathway_id">
                            <option value="">{{ __('All Pathways') }}</option>
                            @foreach($allPathways as $pathway)
                                <option value="{{ $pathway->id }}" {{ request('pathway_id') == $pathway->id ? 'selected' : '' }}>
                                    {{ $pathway->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3 sm:col-span-3">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('reports.pathway-performance') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            {{ __('Clear') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="p-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Pathway Performance Metrics') }}</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    @if($pathwayMetrics->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Pathway') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Cases') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Avg. Compliance') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Avg. Cost Variance') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Avg. Length of Stay') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Avg. Steps Completed') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pathwayMetrics as $metric)
                                        <tr>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $metric->pathway_name }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $metric->total_cases }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm {{ $metric->avg_compliance >= 90 ? 'text-green-600 dark:text-green-400' : ($metric->avg_compliance >= 70 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }} font-semibold">
                                                {{ number_format($metric->avg_compliance, 2) }}%
                                            </td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm {{ $metric->avg_cost_variance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-semibold">
                                                Rp{{ number_format($metric->avg_cost_variance, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ number_format($metric->avg_length_of_stay, 1) }} {{ __('days') }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ number_format($metric->avg_steps_completed, 1) }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm">
                                                <a href="{{ route('pathways.show', $metric->pathway_id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                                                    {{ __('View Pathway') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('No pathway performance data found for the selected criteria.') }}</p>
                    @endif
                </div>
            </div>
            
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-6">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Pathway Step Analysis') }}</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    @if(request('pathway_id') && $stepAnalysis->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Day') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Activity') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Times Performed') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Compliance Rate') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Avg. Actual Cost') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Standard Cost') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Avg. Variance') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($stepAnalysis as $step)
                                        <tr>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $step->day }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $step->activity }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $step->times_performed }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm {{ $step->compliance_rate >= 90 ? 'text-green-600 dark:text-green-400' : ($step->compliance_rate >= 70 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }} font-semibold">
                                                {{ number_format($step->compliance_rate, 2) }}%
                                            </td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">Rp{{ number_format($step->avg_actual_cost, 0, ',', '.') }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">Rp{{ number_format($step->standard_cost, 0, ',', '.') }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm {{ $step->avg_variance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-semibold">
                                                Rp{{ number_format($step->avg_variance, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif(request('pathway_id'))
                        <p class="text-gray-600">{{ __('No step analysis data found for the selected pathway.') }}</p>
                    @else
                        <p class="text-gray-600">{{ __('Please select a specific pathway to view step analysis.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
