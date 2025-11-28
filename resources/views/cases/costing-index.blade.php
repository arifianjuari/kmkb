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
            <a href="{{ route('cases.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('Back to Cases') }}
            </a>
        </div>

        <div id="costing-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Case Costing</span> menampilkan daftar patient cases. Pilih case untuk melihat breakdown biaya dan perbandingan dengan pathway estimate.
            </p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li>Breakdown biaya aktual vs estimasi pathway</li>
                <li>Perbandingan unit cost vs actual cost</li>
                <li>Analisis biaya per kategori</li>
                <li>Perbandingan dengan INA-CBG tariff</li>
            </ul>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('cases.costing.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div>
                            <label for="q" class="block text-sm font-medium text-gray-700">{{ __('Search') }}</label>
                            <input type="text" id="q" name="q" value="{{ $q }}" placeholder="{{ __('MRN, Patient ID, Diagnosis...') }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                        </div>
                        
                        <div>
                            <label for="pathway_id" class="block text-sm font-medium text-gray-700">{{ __('Clinical Pathway') }}</label>
                            <select id="pathway_id" name="pathway_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                                <option value="">{{ __('All Pathways') }}</option>
                                @foreach($pathways as $pathway)
                                    <option value="{{ $pathway->id }}" {{ $pathwayId == $pathway->id ? 'selected' : '' }}>
                                        {{ $pathway->name }} ({{ $pathway->version }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                                {{ __('Filter') }}
                            </button>
                        </div>
                        
                        @if($q || $pathwayId)
                            <div class="flex items-end">
                                <a href="{{ route('cases.costing.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    {{ __('Clear') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Cases List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Select Case to View Costing') }}</h3>
                
                @if($cases->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Medical Record Number') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Patient ID') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Clinical Pathway') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Primary Diagnosis') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actual Cost') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($cases as $case)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $case->medical_record_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $case->patient_id }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $case->clinicalPathway->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $case->primary_diagnosis }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            Rp {{ number_format($case->actual_total_cost ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('cases.costing', $case) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                                                {{ __('View Costing') }}
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
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No cases found') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('No cases match the selected filters.') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

