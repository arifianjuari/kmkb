@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('Edit Clinical Pathway') }}</h2>
                <a href="{{ route('pathways.index') }}" class="btn btn-secondary">{{ __('Back to List') }}</a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('pathways.update', $pathway) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $pathway->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description', $pathway->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="diagnosis_code" class="form-label">{{ __('Diagnosis Code') }}</label>
                                    <input type="text" class="form-control @error('diagnosis_code') is-invalid @enderror" id="diagnosis_code" name="diagnosis_code" value="{{ old('diagnosis_code', $pathway->diagnosis_code) }}" required>
                                    @error('diagnosis_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="version" class="form-label">{{ __('Version') }}</label>
                                    <input type="text" class="form-control @error('version') is-invalid @enderror" id="version" name="version" value="{{ old('version', $pathway->version) }}" required>
                                    @error('version')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="effective_date" class="form-label">{{ __('Effective Date') }}</label>
                                    <input type="date" class="form-control @error('effective_date') is-invalid @enderror" id="effective_date" name="effective_date" value="{{ old('effective_date', $pathway->effective_date->format('Y-m-d')) }}" required>
                                    @error('effective_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">{{ __('Status') }}</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="">{{ __('Select Status') }}</option>
                                        <option value="draft" {{ old('status', $pathway->status) == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                        <option value="active" {{ old('status', $pathway->status) == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="inactive" {{ old('status', $pathway->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">{{ __('Update Pathway') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
