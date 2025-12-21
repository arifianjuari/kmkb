@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Patient Cases') }}</h2>
            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <a href="{{ route('cases.create') }}" class="btn-primary">
                    {{ __('Create New Case') }}
                </a>
                <a href="{{ route('cases.upload') }}" class="btn-success">
                    {{ __('Upload CSV') }}
                </a>
                <a href="{{ route('cases.template') }}" class="btn-success">
                    {{ __('Download Template') }}
                </a>
            </div>
        </div>
        
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <form method="GET" action="{{ route('cases.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="medical_record_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('MRN') }}</label>
                    <input type="text" id="medical_record_number" name="medical_record_number" value="{{ request('medical_record_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div>
                    <label for="pathway_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Pathway') }}</label>
                    <select id="pathway_id" name="pathway_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">{{ __('All Pathways') }}</option>
                        @foreach($pathways as $pathway)
                            <option value="{{ $pathway->id }}" {{ request('pathway_id') == $pathway->id ? 'selected' : '' }}>
                                {{ $pathway->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="admission_date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Admission Date From') }}</label>
                    <input type="date" id="admission_date_from" name="admission_date_from" value="{{ request('admission_date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div>
                    <label for="admission_date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Admission Date To') }}</label>
                    <input type="date" id="admission_date_to" name="admission_date_to" value="{{ request('admission_date_to') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div class="flex justify-end space-x-2 md:col-span-4">
                    <button type="submit" class="btn-primary">
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('cases.index') }}" class="btn-secondary">
                        {{ __('Clear') }}
                    </a>
                </div>
            </form>
        </div>
        
        <div class="p-6">
            @if($cases->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('MRN') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Patient Name') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Pathway') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Admission Date') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Discharge Date') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Compliance %') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Cost Variance') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($cases as $case)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $case->medical_record_number }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $case->patient_name }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $case->clinicalPathway->name }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $case->admission_date->format('d M Y') }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        @if($case->discharge_date)
                                            {{ $case->discharge_date->format('d M Y') }}
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('Not Discharged') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm">
                                        @if($case->compliance_percentage !== null && $case->compliance_percentage !== '')
                                            <span class="{{ $case->compliance_percentage >= 90 ? 'text-green-600 dark:text-green-400' : ($case->compliance_percentage >= 70 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }} font-semibold">
                                                {{ number_format($case->compliance_percentage, 2) }}%
                                            </span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('N/A') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm">
                                        @if($case->cost_variance !== null && $case->cost_variance !== '')
                                            <span class="{{ $case->cost_variance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-semibold">
                                                Rp{{ number_format($case->cost_variance, 2) }}
                                            </span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('N/A') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('cases.show', $case) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700" title="{{ __('View') }}" aria-label="{{ __('View') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                    <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                                                </svg>
                                            </a>
                                            @if(!auth()->user()?->isObserver())
                                            <a href="{{ route('cases.edit', $case) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-biru-dongker-800 hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 dark:border-gray-600 dark:text-biru-dongker-600 dark:hover:bg-biru-dongker-900" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('cases.destroy', $case) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this case?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-gray-600 dark:text-red-400 dark:hover:bg-red-900" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
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
                
                <div class="mt-4">
                    {{ $cases->links() }}
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">{{ __('No patient cases found.') }}</p>
            @endif
        </div>
    </div>
</section>
@endsection
