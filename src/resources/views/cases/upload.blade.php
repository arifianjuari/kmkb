@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('Upload Patient Cases') }}</h2>
                <a href="{{ route('cases.index') }}" class="btn btn-secondary">{{ __('Back to List') }}</a>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Upload CSV File') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cases.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">{{ __('CSV File') }}</label>
                            <input type="file" class="form-control @error('csv_file') is-invalid @enderror" id="csv_file" name="csv_file" accept=".csv" required>
                            @error('csv_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('Please upload a CSV file with patient case data. The file should include headers for medical_record_number, patient_name, clinical_pathway_id, admission_date, discharge_date, primary_diagnosis, additional_diagnoses, total_charges, and insurance_payment.') }}</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">{{ __('Upload Cases') }}</button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('CSV Template') }}</h5>
                </div>
                <div class="card-body">
                    <p>{{ __('Download the CSV template to ensure your data is formatted correctly:') }}</p>
                    <a href="#" class="btn btn-success">{{ __('Download CSV Template') }}</a>
                    
                    <div class="mt-4">
                        <h6>{{ __('Required Columns:') }}</h6>
                        <ul>
                            <li><strong>medical_record_number</strong> - {{ __('Unique medical record number') }}</li>
                            <li><strong>patient_name</strong> - {{ __('Full name of the patient') }}</li>
                            <li><strong>clinical_pathway_id</strong> - {{ __('ID of the clinical pathway') }}</li>
                            <li><strong>admission_date</strong> - {{ __('Admission date (YYYY-MM-DD)') }}</li>
                            <li><strong>discharge_date</strong> - {{ __('Discharge date (YYYY-MM-DD, optional)') }}</li>
                            <li><strong>primary_diagnosis</strong> - {{ __('Primary diagnosis code or description') }}</li>
                            <li><strong>additional_diagnoses</strong> - {{ __('Additional diagnoses (optional)') }}</li>
                            <li><strong>total_charges</strong> - {{ __('Total charges for the case') }}</li>
                            <li><strong>insurance_payment</strong> - {{ __('Payment received from insurance') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
