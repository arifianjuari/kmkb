@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('Compliance Report') }}</h2>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">{{ __('Back to Reports') }}</a>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Report Filters') }}</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.compliance') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="date_from" class="form-label">{{ __('Date From') }}</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from', now()->subMonth()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="date_to" class="form-label">{{ __('Date To') }}</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}">
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
                                    <label for="compliance_range" class="form-label">{{ __('Compliance Range') }}</label>
                                    <select class="form-control" id="compliance_range" name="compliance_range">
                                        <option value="">{{ __('All Ranges') }}</option>
                                        <option value="high" {{ request('compliance_range') == 'high' ? 'selected' : '' }}>{{ __('High (≥ 90%)') }}</option>
                                        <option value="medium" {{ request('compliance_range') == 'medium' ? 'selected' : '' }}>{{ __('Medium (70-89%)') }}</option>
                                        <option value="low" {{ request('compliance_range') == 'low' ? 'selected' : '' }}>{{ __('Low (< 70%)') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">{{ __('Filter') }}</button>
                            <a href="{{ route('reports.compliance') }}" class="btn btn-secondary">{{ __('Clear') }}</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">{{ __('High Compliance') }}</h5>
                                    <h2>{{ $highComplianceCount }}</h2>
                                    <p class="mb-0">{{ __('≥ 90%') }}</p>
                                </div>
                                <i class="fas fa-thumbs-up fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">{{ __('Medium Compliance') }}</h5>
                                    <h2>{{ $mediumComplianceCount }}</h2>
                                    <p class="mb-0">{{ __('70-89%') }}</p>
                                </div>
                                <i class="fas fa-exclamation-triangle fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">{{ __('Low Compliance') }}</h5>
                                    <h2>{{ $lowComplianceCount }}</h2>
                                    <p class="mb-0">{{ __('< 70%') }}</p>
                                </div>
                                <i class="fas fa-thumbs-down fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5>{{ __('Compliance Details') }}</h5>
                </div>
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
                                        <th>{{ __('Compliance %') }}</th>
                                        <th>{{ __('Status') }}</th>
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
                                                <span class="{{ $case->compliance_percentage >= 90 ? 'text-success' : ($case->compliance_percentage >= 70 ? 'text-warning' : 'text-danger') }}">
                                                    {{ number_format($case->compliance_percentage, 2) }}%
                                                </span>
                                            </td>
                                            <td>
                                                @if($case->compliance_percentage >= 90)
                                                    <span class="badge bg-success">{{ __('High') }}</span>
                                                @elseif($case->compliance_percentage >= 70)
                                                    <span class="badge bg-warning">{{ __('Medium') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('Low') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('cases.show', $case) }}" class="btn btn-sm btn-info">{{ __('View Case') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{ $cases->links() }}
                    @else
                        <p>{{ __('No cases found matching the selected criteria.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
