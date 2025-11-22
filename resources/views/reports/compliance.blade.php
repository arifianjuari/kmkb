@extends('layouts.app')

@section('content')
<div class="mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Compliance Report') }}</h2>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Back to Reports') }}
            </a>
        </div>
            
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Report Filters') }}</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <form method="GET" action="{{ route('reports.compliance') }}">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-2">
                                <label for="date_from" class="block text-sm font-medium text-gray-700">{{ __('Date From') }}</label>
                                <div class="mt-1">
                                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from', now()->subMonth()->format('Y-m-d')) }}" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="date_to" class="block text-sm font-medium text-gray-700">{{ __('Date To') }}</label>
                                <div class="mt-1">
                                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <label for="pathway_id" class="block text-sm font-medium text-gray-700">{{ __('Pathway') }}</label>
                                <div class="mt-1">
                                    <select id="pathway_id" name="pathway_id" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">{{ __('All Pathways') }}</option>
                                        @foreach($pathways as $pathway)
                                            <option value="{{ $pathway->id }}" {{ request('pathway_id') == $pathway->id ? 'selected' : '' }}>
                                                {{ $pathway->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <label for="compliance_range" class="block text-sm font-medium text-gray-700">{{ __('Compliance Range') }}</label>
                                <div class="mt-1">
                                    <select id="compliance_range" name="compliance_range" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">{{ __('All Ranges') }}</option>
                                        <option value="high" {{ request('compliance_range') == 'high' ? 'selected' : '' }}>{{ __('High (≥ 90%)') }}</option>
                                        <option value="medium" {{ request('compliance_range') == 'medium' ? 'selected' : '' }}>{{ __('Medium (70-89%)') }}</option>
                                        <option value="low" {{ request('compliance_range') == 'low' ? 'selected' : '' }}>{{ __('Low (< 70%)') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Filter') }}
                            </button>
                            <a href="{{ route('reports.compliance') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Clear') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div class="bg-green-500 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-white">{{ __('High Compliance') }}</h3>
                                <div class="mt-1 text-3xl font-semibold text-white">{{ $highComplianceCount }}</div>
                                <p class="mt-1 text-sm text-green-100">{{ __('≥ 90%') }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <!-- Thumbs up icon -->
                                <svg class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-500 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-white">{{ __('Medium Compliance') }}</h3>
                                <div class="mt-1 text-3xl font-semibold text-white">{{ $mediumComplianceCount }}</div>
                                <p class="mt-1 text-sm text-yellow-100">{{ __('70-89%') }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <!-- Warning icon -->
                                <svg class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-red-500 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-white">{{ __('Low Compliance') }}</h3>
                                <div class="mt-1 text-3xl font-semibold text-white">{{ $lowComplianceCount }}</div>
                                <p class="mt-1 text-sm text-red-100">{{ __('< 70%') }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <!-- Thumbs down icon -->
                                <svg class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018c.163 0 .326.02.485.06L17 4m0 0v9m0-9h2.765a2 2 0 011.789 2.894l-3.5 7A2 2 0 0118.264 15H17m0 0v2m0-2h-2M7 11v2m0-2H5" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-6">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Compliance Details') }}</h3>
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
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Compliance %') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($cases as $case)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $case->medical_record_number }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $case->patient_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $case->clinicalPathway->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $case->admission_date->format('d M Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $case->compliance_percentage >= 90 ? 'text-green-600' : ($case->compliance_percentage >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ number_format($case->compliance_percentage, 2) }}%
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($case->compliance_percentage >= 90)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        {{ __('High') }}
                                                    </span>
                                                @elseif($case->compliance_percentage >= 70)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        {{ __('Medium') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        {{ __('Low') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('cases.show', $case) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
