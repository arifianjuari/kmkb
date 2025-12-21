@extends('layouts.app')

@section('content')
<div class="mx-auto py-6 sm:px-6 lg:px-8">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Cost Variance Report') }}</h2>
            <a href="{{ route('reports.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                {{ __('Back to Reports') }}
            </a>
        </div>
            
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Report Filters') }}</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <form method="GET" action="{{ route('reports.cost-variance') }}">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-2">
                                <label for="date_from" class="block text-sm font-medium text-gray-700">{{ __('Date From') }}</label>
                                <div class="mt-1">
                                    <input type="date" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700" id="date_from" name="date_from" value="{{ request('date_from', now()->subMonth()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="date_to" class="block text-sm font-medium text-gray-700">{{ __('Date To') }}</label>
                                <div class="mt-1">
                                    <input type="date" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700" id="date_to" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="pathway_id" class="block text-sm font-medium text-gray-700">{{ __('Pathway') }}</label>
                                <div class="mt-1">
                                    <select id="pathway_id" name="pathway_id" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                        <option value="">{{ __('All Pathways') }}</option>
                                        @foreach($pathways as $pathway)
                                            <option value="{{ $pathway->id }}" {{ request('pathway_id') == $pathway->id ? 'selected' : '' }}>
                                                {{ $pathway->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="variance_range" class="block text-sm font-medium text-gray-700">{{ __('Variance Range') }}</label>
                                <div class="mt-1">
                                    <select id="variance_range" name="variance_range" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                        <option value="">{{ __('All Ranges') }}</option>
                                        <option value="under" {{ request('variance_range') == 'under' ? 'selected' : '' }}>{{ __('Under Budget') }}</option>
                                        <option value="over" {{ request('variance_range') == 'over' ? 'selected' : '' }}>{{ __('Over Budget') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                                {{ __('Filter') }}
                            </button>
                            <a href="{{ route('reports.cost-variance') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                                {{ __('Clear') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div class="bg-red-500 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-white">{{ __('Over Budget') }}</h3>
                                <div class="mt-1 text-3xl font-semibold text-white">{{ $overBudgetCount }}</div>
                                <p class="mt-1 text-sm text-red-100">{{ __('Cases exceeding budget') }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <!-- Arrow up icon -->
                                <svg class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-500 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-white">{{ __('Under Budget') }}</h3>
                                <div class="mt-1 text-3xl font-semibold text-white">{{ $underBudgetCount }}</div>
                                <p class="mt-1 text-sm text-green-100">{{ __('Cases under budget') }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <!-- Arrow down icon -->
                                <svg class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-500 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-white">{{ __('Total Variance') }}</h3>
                                <div class="mt-1 text-3xl font-semibold text-white">{{ number_format($totalVariance, 2) }}</div>
                                <p class="mt-1 text-sm text-blue-100">{{ __('Overall financial impact') }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <!-- Dollar sign icon -->
                                <svg class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-6">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Cost Variance Details') }}</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    @if($cases->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('MRN') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Patient Name') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Pathway') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Admission Date') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Charges') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('INA CBG Tariff') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Full Standard Cost') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Variance') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Variance %') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($cases as $case)
                                        <tr>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $case->medical_record_number }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $case->patient_name }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $case->clinicalPathway->name }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $case->admission_date->format('d M Y') }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">Rp{{ number_format($case->total_charges, 0, ',', '.') }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">Rp{{ number_format($case->standard_cost, 0, ',', '.') }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm text-blue-600 dark:text-blue-400 font-semibold">Rp{{ number_format($case->full_standard_cost, 0, ',', '.') }}</td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm {{ $case->cost_variance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-semibold">
                                                Rp{{ number_format($case->cost_variance, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm {{ $case->cost_variance_percentage >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-semibold">
                                                @if($case->standard_cost > 0)
                                                    {{ number_format($case->cost_variance_percentage, 2) }}%
                                                @else
                                                    {{ __('N/A') }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-2 whitespace-nowrap text-sm">
                                                <a href="{{ route('cases.show', $case) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                                                    {{ __('View Case') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $cases->links() }}
                        </div>
                    @else
                        <p class="text-gray-700">{{ __('No cases found matching the selected criteria.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
