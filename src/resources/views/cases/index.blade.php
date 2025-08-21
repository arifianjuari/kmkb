@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('Patient Cases') }}</h2>
                <div>
                    <a href="{{ route('cases.create') }}" class="btn btn-primary">{{ __('Create New Case') }}</a>
                    <a href="{{ route('cases.upload-form') }}" class="btn btn-success">{{ __('Upload CSV') }}</a>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('cases.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="medical_record_number" class="form-label">{{ __('MRN') }}</label>
                                    <input type="text" class="form-control" id="medical_record_number" name="medical_record_number" value="{{ request('medical_record_number') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="pathway_id" class="form-label">{{ __('Pathway') }}</label>
                                    <select class="form-control" id="pathway_id" name="pathway_id">
                                        <option value="">{{ __('All Pathways') }}</option>
                                        @foreach($pathways as $pathway)
                                            <option value="{{ $pathway->id }}" {{ request('pathway_id') == $pathway->id ? 'selected' : '' }}>
                                                {{ $pathway->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="admission_date_from" class="form-label">{{ __('Admission Date From') }}</label>
                                    <input type="date" class="form-control" id="admission_date_from" name="admission_date_from" value="{{ request('admission_date_from') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="admission_date_to" class="form-label">{{ __('Admission Date To') }}</label>
                                    <input type="date" class="form-control" id="admission_date_to" name="admission_date_to" value="{{ request('admission_date_to') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">{{ __('Filter') }}</button>
                            <a href="{{ route('cases.index') }}" class="btn btn-secondary">{{ __('Clear') }}</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    @if($cases->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('MRN') }}</th>
                                        <th>{{ __('Patient Name') }}</th>
                                        <th>{{ __('Pathway') }}</th>
                                        <th>{{ __('Admission Date') }}</th>
                                        <th>{{ __('Discharge Date') }}</th>
                                        <th>{{ __('Compliance %') }}</th>
                                        <th>{{ __('Cost Variance') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cases as $case)
                                        <tr>
                                            <td>{{ $case->medical_record_number }}</td>
                                            <td>{{ $case->patient_name }}</td>
                                            <td>{{ $case->clinicalPathway->name }}</td>
                                            <td>{{ $case->admission_date->format('d M Y') }}</td>
                                            <td>
                                                @if($case->discharge_date)
                                                    {{ $case->discharge_date->format('d M Y') }}
                                                @else
                                                    <span class="text-muted">{{ __('Not Discharged') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($case->compliance_percentage !== null)
                                                    <span class="{{ $case->compliance_percentage >= 90 ? 'text-success' : ($case->compliance_percentage >= 70 ? 'text-warning' : 'text-danger') }}">
                                                        {{ number_format($case->compliance_percentage, 2) }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">{{ __('N/A') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($case->cost_variance !== null)
                                                    <span class="{{ $case->cost_variance <= 0 ? 'text-success' : 'text-danger' }}">
                                                        Rp{{ number_format($case->cost_variance, 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">{{ __('N/A') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('cases.show', $case) }}" class="btn btn-sm btn-info">{{ __('View') }}</a>
                                                <a href="{{ route('cases.edit', $case) }}" class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                                                
                                                <form action="{{ route('cases.destroy', $case) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure you want to delete this case?') }}')">{{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{ $cases->links() }}
                    @else
                        <p>{{ __('No patient cases found.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
