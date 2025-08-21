@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('Create Patient Case') }}</h2>
                <a href="{{ route('cases.index') }}" class="btn btn-secondary">{{ __('Back to List') }}</a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('cases.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="medical_record_number" class="form-label">{{ __('Medical Record Number') }}</label>
                                    <input type="text" class="form-control @error('medical_record_number') is-invalid @enderror" id="medical_record_number" name="medical_record_number" value="{{ old('medical_record_number') }}" required>
                                    @error('medical_record_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="patient_name" class="form-label">{{ __('Patient Name') }}</label>
                                    <input type="text" class="form-control @error('patient_name') is-invalid @enderror" id="patient_name" name="patient_name" value="{{ old('patient_name') }}" required>
                                    @error('patient_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="clinical_pathway_id" class="form-label">{{ __('Clinical Pathway') }}</label>
                                    <select class="form-control @error('clinical_pathway_id') is-invalid @enderror" id="clinical_pathway_id" name="clinical_pathway_id" required>
                                        <option value="">{{ __('Select Pathway') }}</option>
                                        @foreach($pathways as $pathway)
                                            <option value="{{ $pathway->id }}" {{ old('clinical_pathway_id') == $pathway->id ? 'selected' : '' }}>
                                                {{ $pathway->name }} ({{ $pathway->diagnosis_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('clinical_pathway_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="admission_date" class="form-label">{{ __('Admission Date') }}</label>
                                    <input type="date" class="form-control @error('admission_date') is-invalid @enderror" id="admission_date" name="admission_date" value="{{ old('admission_date') }}" required>
                                    @error('admission_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="discharge_date" class="form-label">{{ __('Discharge Date') }}</label>
                                    <input type="date" class="form-control @error('discharge_date') is-invalid @enderror" id="discharge_date" name="discharge_date" value="{{ old('discharge_date') }}">
                                    @error('discharge_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="primary_diagnosis" class="form-label">{{ __('Primary Diagnosis') }}</label>
                                    <input type="text" class="form-control @error('primary_diagnosis') is-invalid @enderror" id="primary_diagnosis" name="primary_diagnosis" value="{{ old('primary_diagnosis') }}" required>
                                    @error('primary_diagnosis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="additional_diagnoses" class="form-label">{{ __('Additional Diagnoses') }}</label>
                            <textarea class="form-control @error('additional_diagnoses') is-invalid @enderror" id="additional_diagnoses" name="additional_diagnoses" rows="3">{{ old('additional_diagnoses') }}</textarea>
                            @error('additional_diagnoses')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="total_charges" class="form-label">{{ __('Total Charges') }}</label>
                                    <input type="number" class="form-control @error('total_charges') is-invalid @enderror" id="total_charges" name="total_charges" step="0.01" min="0" value="{{ old('total_charges') }}" required>
                                    @error('total_charges')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="insurance_payment" class="form-label">{{ __('Insurance Payment') }}</label>
                                    <input type="number" class="form-control @error('insurance_payment') is-invalid @enderror" id="insurance_payment" name="insurance_payment" step="0.01" min="0" value="{{ old('insurance_payment') }}" required>
                                    @error('insurance_payment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">{{ __('Create Case') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
