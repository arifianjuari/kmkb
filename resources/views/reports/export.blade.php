@extends('layouts.app')

@section('content')
<div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Export Data') }}</h2>
            <a href="{{ route('reports.index') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0">
                {{ __('Back to Reports') }}
            </a>
        </div>
        
        <div class="px-6 py-4 bg-blue-50 border-b border-gray-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <!-- Info icon -->
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">{{ __('Select the data you want to export and choose your preferred format. Exports will be generated and available for download shortly.') }}</p>
                </div>
            </div>
        </div>
        
        <div class="p-6 border-b border-gray-200">
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Export Options') }}</h3>
                <form method="POST" action="{{ route('reports.export.generate') }}">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2"><strong>{{ __('Data to Export') }}</strong></label>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <div class="flex items-start mb-3">
                                    <div class="flex items-center h-5">
                                        <input class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" type="checkbox" id="export_cases" name="export_data[]" value="cases" checked>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="export_cases" class="font-medium text-gray-700">{{ __('Patient Cases') }}</label>
                                        <p class="text-gray-500">{{ __('All patient case records with details') }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start mb-3">
                                    <div class="flex items-center h-5">
                                        <input class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" type="checkbox" id="export_pathways" name="export_data[]" value="pathways">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="export_pathways" class="font-medium text-gray-700">{{ __('Clinical Pathways') }}</label>
                                        <p class="text-gray-500">{{ __('All clinical pathway definitions and steps') }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start mb-3">
                                    <div class="flex items-center h-5">
                                        <input class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" type="checkbox" id="export_users" name="export_data[]" value="users">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="export_users" class="font-medium text-gray-700">{{ __('Users') }}</label>
                                        <p class="text-gray-500">{{ __('User accounts and roles') }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex items-start mb-3">
                                    <div class="flex items-center h-5">
                                        <input class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" type="checkbox" id="export_audit_logs" name="export_data[]" value="audit_logs">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="export_audit_logs" class="font-medium text-gray-700">{{ __('Audit Logs') }}</label>
                                        <p class="text-gray-500">{{ __('System activity and user actions') }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start mb-3">
                                    <div class="flex items-center h-5">
                                        <input class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" type="checkbox" id="export_compliance" name="export_data[]" value="compliance">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="export_compliance" class="font-medium text-gray-700">{{ __('Compliance Reports') }}</label>
                                        <p class="text-gray-500">{{ __('Compliance metrics and analysis') }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start mb-3">
                                    <div class="flex items-center h-5">
                                        <input class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" type="checkbox" id="export_cost_variance" name="export_data[]" value="cost_variance">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="export_cost_variance" class="font-medium text-gray-700">{{ __('Cost Variance Reports') }}</label>
                                        <p class="text-gray-500">{{ __('Financial performance data') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2"><strong>{{ __('Export Format') }}</strong></label>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" type="radio" id="format_csv" name="format" value="csv" checked>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="format_csv" class="font-medium text-gray-700">{{ __('CSV') }}</label>
                                    <p class="text-gray-500">{{ __('Comma-separated values') }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" type="radio" id="format_excel" name="format" value="excel">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="format_excel" class="font-medium text-gray-700">{{ __('Excel') }}</label>
                                    <p class="text-gray-500">{{ __('Microsoft Excel format') }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" type="radio" id="format_pdf" name="format" value="pdf">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="format_pdf" class="font-medium text-gray-700">{{ __('PDF') }}</label>
                                    <p class="text-gray-500">{{ __('Portable Document Format') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2"><strong>{{ __('Date Range') }}</strong></label>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700">{{ __('From') }}</label>
                                <input type="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="date_from" name="date_from" value="{{ now()->subMonth()->format('Y-m-d') }}">
                            </div>
                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700">{{ __('To') }}</label>
                                <input type="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="date_to" name="date_to" value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <!-- Export icon -->
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Generate Export') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="p-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Recent Exports') }}</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    @if($recentExports->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('File Name') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Data Type') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Format') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Generated At') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentExports as $export)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $export->file_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $export->data_type)) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($export->format == 'csv')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">CSV</span>
                                                @elseif($export->format == 'excel')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Excel</span>
                                                @elseif($export->format == 'pdf')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">PDF</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $export->created_at->format('d M Y H:i') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($export->status == 'completed')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ __('Completed') }}</span>
                                                @elseif($export->status == 'processing')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ __('Processing') }}</span>
                                                @elseif($export->status == 'failed')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ __('Failed') }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($export->status == 'completed' && $export->file_path)
                                                    <a href="{{ route('reports.export.download', $export) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        <!-- Download icon -->
                                                        <svg class="-ml-1 mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                        {{ __('Download') }}
                                                    </a>
                                                @elseif($export->status == 'failed')
                                                    <button class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" disabled>
                                                        <!-- Exclamation icon -->
                                                        <svg class="-ml-1 mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                        {{ __('Failed') }}
                                                    </button>
                                                @else
                                                    <button class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" disabled>
                                                        <!-- Clock icon -->
                                                        <svg class="-ml-1 mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                        </svg>
                                                        {{ __('Processing') }}
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('No recent exports found.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
