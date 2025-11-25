@extends('layouts.app')

@section('content')
<section class="mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Create Patient Case') }}</h2>
            <a href="{{ route('cases.index') }}" class="btn-secondary">
                {{ __('Back to List') }}
            </a>
        </div>
            
        <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('cases.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="medical_record_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Medical Record Number') }}</label>
                            <div class="mt-1">
                                <input type="text" id="medical_record_number" name="medical_record_number" value="{{ old('medical_record_number') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('medical_record_number') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            @error('medical_record_number')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Patient Name') }}</label>
                            <div class="mt-1">
                                <input type="text" id="patient_id" name="patient_id" value="{{ old('patient_id') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('patient_id') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            @error('patient_id')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 mt-6">
                        <div class="sm:col-span-3">
                            <label for="clinical_pathway_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Clinical Pathway') }}</label>
                            <div class="mt-1">
                                <select id="clinical_pathway_id" name="clinical_pathway_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('clinical_pathway_id') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">{{ __('Select Pathway') }}</option>
                                    @foreach($pathways as $pathway)
                                        <option value="{{ $pathway->id }}" {{ old('clinical_pathway_id') == $pathway->id ? 'selected' : '' }}>
                                            {{ $pathway->name }} ({{ $pathway->diagnosis_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('clinical_pathway_id')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="admission_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Admission Date') }}</label>
                            <div class="mt-1">
                                <input type="date" id="admission_date" name="admission_date" value="{{ old('admission_date') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('admission_date') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            @error('admission_date')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 mt-6">
                        <div class="sm:col-span-3">
                            <label for="discharge_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Discharge Date') }}</label>
                            <div class="mt-1">
                                <input type="date" id="discharge_date" name="discharge_date" value="{{ old('discharge_date') }}" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('discharge_date') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            @error('discharge_date')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="primary_diagnosis" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Primary Diagnosis') }}</label>
                            <div class="mt-1">
                                <input type="text" id="primary_diagnosis" name="primary_diagnosis" value="{{ old('primary_diagnosis') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('primary_diagnosis') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            @error('primary_diagnosis')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="ina_cbg_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('INA CBG Code') }}</label>
                            <div class="mt-1 relative">
                                <input type="text" id="ina_cbg_code" name="ina_cbg_code" value="{{ old('ina_cbg_code') }}" required autocomplete="off" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('ina_cbg_code') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <div id="cbg-code-suggestions" class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-md overflow-hidden dark:bg-gray-700 hidden">
                                    <ul class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm dark:bg-gray-700">
                                    </ul>
                                </div>
                            </div>
                            @error('ina_cbg_code')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-3 sm:col-start-4">
                            <label for="additional_diagnoses" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Additional Diagnoses') }}</label>
                            <div class="mt-1">
                                <textarea id="additional_diagnoses" name="additional_diagnoses" rows="3" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('additional_diagnoses') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('additional_diagnoses') }}</textarea>
                            </div>
                            @error('additional_diagnoses')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 mt-6">
                        <div class="sm:col-span-3">
                            <label for="actual_total_cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Actual Total Cost') }}</label>
                            <div class="mt-1">
                                <input type="number" id="actual_total_cost" name="actual_total_cost" step="0.01" min="0" value="{{ old('actual_total_cost') }}" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('actual_total_cost') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            @error('actual_total_cost')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="ina_cbg_tariff" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('INA CBG Tariff') }}</label>
                            <div class="mt-1">
                                <input type="number" id="ina_cbg_tariff" name="ina_cbg_tariff" step="0.01" min="0" value="{{ old('ina_cbg_tariff') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('ina_cbg_tariff') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            @error('ina_cbg_tariff')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="annotation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Annotation') }}</label>
                        <div class="mt-1">
                            <textarea id="annotation" name="annotation" rows="3" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('annotation') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="{{ __('Catatan atau komentar atas kasus yang dinilai') }}">{{ old('annotation') }}</textarea>
                        </div>
                        @error('annotation')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-2">
                        <a href="{{ route('cases.index') }}" class="btn-secondary">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn-primary">
                            {{ __('Create Case') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cbgCodeInput = document.getElementById('ina_cbg_code');
        const cbgSuggestions = document.getElementById('cbg-code-suggestions');
        const tariffInput = document.getElementById('ina_cbg_tariff');
        
        let timeout = null;

        // If code already has value (e.g., after validation error), auto-fetch tariff on load
        if (cbgCodeInput && cbgCodeInput.value) {
            fetch(`/jkn-cbg-codes/tariff?code=${encodeURIComponent(cbgCodeInput.value)}`)
                .then(response => response.json())
                .then(tariffData => {
                    if (tariffData && typeof tariffData.tariff !== 'undefined' && tariffData.tariff !== null) {
                        tariffInput.value = tariffData.tariff;
                    }
                })
                .catch(() => {/* noop */});
        }
        
        cbgCodeInput.addEventListener('input', function() {
            const query = this.value;
            
            // Clear previous timeout
            if (timeout) {
                clearTimeout(timeout);
            }
            
            // If query is empty, hide suggestions
            if (!query) {
                cbgSuggestions.classList.add('hidden');
                return;
            }
            
            // Set new timeout for debouncing
            timeout = setTimeout(function() {
                fetch(`/jkn-cbg-codes/search?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        // Clear previous suggestions
                        cbgSuggestions.querySelector('ul').innerHTML = '';
                        
                        // Add new suggestions
                        if (data.length > 0) {
                            data.forEach(code => {
                                const li = document.createElement('li');
                                li.className = 'cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 dark:hover:bg-gray-600';
                                li.innerHTML = `
                                    <div class="flex items-center">
                                        <span class="font-normal block truncate dark:text-white">${code.code} - ${code.name}</span>
                                    </div>
                                `;
                                li.addEventListener('click', function() {
                                    cbgCodeInput.value = code.code;
                                    cbgSuggestions.classList.add('hidden');
                                    
                                    // Auto-fill tariff
                                    fetch(`/jkn-cbg-codes/tariff?code=${encodeURIComponent(code.code)}`)
                                        .then(response => response.json())
                                        .then(tariffData => {
                                            if (tariffData.tariff) {
                                                tariffInput.value = tariffData.tariff;
                                            }
                                        })
                                        .catch(err => {
                                            console.error('Failed to fetch tariff', err);
                                        });
                                });
                                cbgSuggestions.querySelector('ul').appendChild(li);
                            });
                            cbgSuggestions.classList.remove('hidden');
                        } else {
                            cbgSuggestions.classList.add('hidden');
                        }
                    })
                    .catch(err => {
                        console.error('Failed to fetch CBG code suggestions', err);
                        cbgSuggestions.classList.add('hidden');
                    });
            }, 300); // 300ms debounce
        });

        // Show initial suggestions when focusing the input (top results)
        cbgCodeInput.addEventListener('focus', function() {
            // If there's an existing timeout, clear it to avoid double fetch
            if (timeout) clearTimeout(timeout);
            const query = this.value || '';
            fetch(`/jkn-cbg-codes/search?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    cbgSuggestions.querySelector('ul').innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(code => {
                            const li = document.createElement('li');
                            li.className = 'cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 dark:hover:bg-gray-600';
                            li.innerHTML = `
                                <div class="flex items-center">
                                    <span class="font-normal block truncate dark:text-white">${code.code} - ${code.name}</span>
                                </div>
                            `;
                            li.addEventListener('click', function() {
                                cbgCodeInput.value = code.code;
                                cbgSuggestions.classList.add('hidden');
                                fetch(`/jkn-cbg-codes/tariff?code=${encodeURIComponent(code.code)}`)
                                    .then(response => response.json())
                                    .then(tariffData => {
                                        if (tariffData.tariff) {
                                            tariffInput.value = tariffData.tariff;
                                        }
                                    })
                                    .catch(err => console.error('Failed to fetch tariff', err));
                            });
                            cbgSuggestions.querySelector('ul').appendChild(li);
                        });
                        cbgSuggestions.classList.remove('hidden');
                    } else {
                        cbgSuggestions.classList.add('hidden');
                    }
                })
                .catch(err => console.error('Failed to fetch CBG code suggestions', err));
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!cbgCodeInput.contains(e.target) && !cbgSuggestions.contains(e.target)) {
                cbgSuggestions.classList.add('hidden');
            }
        });

        // Hide suggestions on Escape key
        cbgCodeInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cbgSuggestions.classList.add('hidden');
                cbgCodeInput.blur();
            }
        });

        // Slight delay on blur to allow click selection
        cbgCodeInput.addEventListener('blur', function() {
            setTimeout(() => cbgSuggestions.classList.add('hidden'), 150);
        });
    });
</script>
@endsection
