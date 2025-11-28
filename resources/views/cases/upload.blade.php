@extends('layouts.app')

@section('content')
<div class="mx-auto py-6 sm:px-6 lg:px-8">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Upload Patient Cases') }}</h2>
            <a href="{{ route('cases.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                {{ __('Back to List') }}
            </a>
        </div>
            
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Upload Excel/CSV File') }}</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <form action="{{ route('cases.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="csv_file" class="block text-sm font-medium text-gray-700">{{ __('Excel/CSV File') }}</label>
                            <div class="mt-1">
                                <input type="file" id="csv_file" name="csv_file" accept=".xlsx,.xls,.csv" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 @error('csv_file') border-red-500 @enderror">
                            </div>
                            @error('csv_file')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">{{ __('Please upload an Excel or CSV file with patient case data. The file should include headers for medical_record_number, patient_name, clinical_pathway_id, admission_date, discharge_date, primary_diagnosis, ina_cbg_code, actual_total_cost, and ina_cbg_tariff.') }}</p>
                        </div>
                        
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                            {{ __('Upload Cases') }}
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Excel Template') }}</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <p class="text-gray-700 mb-4">{{ __('Download the Excel template to ensure your data is formatted correctly:') }}</p>
                    <a href="{{ route('cases.template') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        {{ __('Download Excel Template') }}
                    </a>
                    
                    <div class="mt-6">
                        <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Required Columns:') }}</h4>
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong>medical_record_number</strong> - {{ __('Unique medical record number') }}</li>
                            <li><strong>patient_name</strong> - {{ __('Full name of the patient') }}</li>
                            <li><strong>clinical_pathway_id</strong> - {{ __('ID of the clinical pathway') }}</li>
                            <li><strong>admission_date</strong> - {{ __('Admission date (YYYY-MM-DD)') }}</li>
                            <li><strong>discharge_date</strong> - {{ __('Discharge date (YYYY-MM-DD, optional)') }}</li>
                            <li><strong>primary_diagnosis</strong> - {{ __('Primary diagnosis code or description') }}</li>
                            <li><strong>ina_cbg_code</strong> - {{ __('INA CBG code') }}</li>
                            <li><strong>actual_total_cost</strong> - {{ __('Actual total cost for the case') }}</li>
                            <li><strong>ina_cbg_tariff</strong> - {{ __('INA CBG tariff') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
