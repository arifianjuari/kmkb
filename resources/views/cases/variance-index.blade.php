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
            <a href="{{ route('cases.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('Back to Cases') }}
            </a>
        </div>

        <div id="variance-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Variance Analysis</span> menampilkan daftar patient cases. Pilih case untuk melihat analisis variance antara planned steps (pathway) dengan actual steps yang dilakukan.
            </p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li>Step compliance analysis (planned vs actual)</li>
                <li>Cost variance per step</li>
                <li>Identifikasi custom steps</li>
                <li>Summary variance statistics</li>
            </ul>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('cases.variance.index') }}" class="space-y-4">
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
                                <a href="{{ route('cases.variance.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
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
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Select Case to View Variance Analysis') }}</h3>
                
                @if($cases->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Medical Record Number') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Patient ID') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Clinical Pathway') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Primary Diagnosis') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Compliance') }}</th>
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ ($case->compliance_percentage ?? 0) >= 80 ? 'bg-green-100 text-green-800' : '' }}
                                                {{ ($case->compliance_percentage ?? 0) >= 50 && ($case->compliance_percentage ?? 0) < 80 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ ($case->compliance_percentage ?? 0) < 50 ? 'bg-red-100 text-red-800' : '' }}
                                            ">
                                                {{ number_format($case->compliance_percentage ?? 0, 1) }}%
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('cases.variance', $case) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                                                {{ __('View Analysis') }}
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
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

