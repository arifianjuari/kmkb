@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('Cost Variance Report') }}</h2>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">{{ __('Back to Reports') }}</a>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Report Filters') }}</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.cost-variance') }}">
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
                                    <label for="variance_range" class="form-label">{{ __('Variance Range') }}</label>
                                    <select class="form-control" id="variance_range" name="variance_range">
                                        <option value="">{{ __('All Ranges') }}</option>
                                        <option value="under" {{ request('variance_range') == 'under' ? 'selected' : '' }}>{{ __('Under Budget') }}</option>
                                        <option value="over" {{ request('variance_range') == 'over' ? 'selected' : '' }}>{{ __('Over Budget') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">{{ __('Filter') }}</button>
                            <a href="{{ route('reports.cost-variance') }}" class="btn btn-secondary">{{ __('Clear') }}</a>
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
                                    <h5 class="card-title">{{ __('Under Budget') }}</h5>
                                    <h2>{{ $underBudgetCount }}</h2>
                                    <p class="mb-0">{{ __('Cases under budget') }}</p>
                                </div>
                                <i class="fas fa-arrow-down fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">{{ __('Total Variance') }}</h5>
                                    <h2>Rp{{ number_format($totalVariance, 2) }}</h2>
                                    <p class="mb-0">{{ __('Overall variance') }}</p>
                                </div>
                                <i class="fas fa-balance-scale fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">{{ __('Over Budget') }}</h5>
                                    <h2>{{ $overBudgetCount }}</h2>
                                    <p class="mb-0">{{ __('Cases over budget') }}</p>
                                </div>
                                <i class="fas fa-arrow-up fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5>{{ __('Cost Variance Details') }}</h5>
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
                                        <th>{{ __('Total Charges') }}</th>
                                        <th>{{ __('Standard Cost') }}</th>
                                        <th>{{ __('Variance') }}</th>
                                        <th>{{ __('Variance %') }}</th>
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
                                            <td>Rp{{ number_format($case->total_charges, 2) }}</td>
                                            <td>Rp{{ number_format($case->standard_cost, 2) }}</td>
                                            <td>
                                                <span class="{{ $case->cost_variance <= 0 ? 'text-success' : 'text-danger' }}">
                                                    Rp{{ number_format($case->cost_variance, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($case->standard_cost > 0)
                                                    <span class="{{ $case->variance_percentage <= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ number_format($case->variance_percentage, 2) }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">{{ __('N/A') }}</span>
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
