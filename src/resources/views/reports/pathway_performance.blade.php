@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('Pathway Performance Report') }}</h2>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">{{ __('Back to Reports') }}</a>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Report Filters') }}</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.pathway-performance') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date_from" class="form-label">{{ __('Date From') }}</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from', now()->subMonth()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date_to" class="form-label">{{ __('Date To') }}</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
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
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">{{ __('Filter') }}</button>
                            <a href="{{ route('reports.pathway-performance') }}" class="btn btn-secondary">{{ __('Clear') }}</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Pathway Performance Metrics') }}</h5>
                </div>
                <div class="card-body">
                    @if($pathwayMetrics->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Pathway') }}</th>
                                        <th>{{ __('Total Cases') }}</th>
                                        <th>{{ __('Avg. Compliance') }}</th>
                                        <th>{{ __('Avg. Cost Variance') }}</th>
                                        <th>{{ __('Avg. Length of Stay') }}</th>
                                        <th>{{ __('Avg. Steps Completed') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pathwayMetrics as $metric)
                                        <tr>
                                            <td>{{ $metric->pathway_name }}</td>
                                            <td>{{ $metric->total_cases }}</td>
                                            <td>
                                                <span class="{{ $metric->avg_compliance >= 90 ? 'text-success' : ($metric->avg_compliance >= 70 ? 'text-warning' : 'text-danger') }}">
                                                    {{ number_format($metric->avg_compliance, 2) }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="{{ $metric->avg_cost_variance <= 0 ? 'text-success' : 'text-danger' }}">
                                                    Rp{{ number_format($metric->avg_cost_variance, 2) }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($metric->avg_length_of_stay, 1) }} {{ __('days') }}</td>
                                            <td>{{ number_format($metric->avg_steps_completed, 1) }}</td>
                                            <td>
                                                <a href="{{ route('pathways.show', $metric->pathway_id) }}" class="btn btn-sm btn-info">{{ __('View Pathway') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>{{ __('No pathway performance data found for the selected criteria.') }}</p>
                    @endif
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5>{{ __('Pathway Step Analysis') }}</h5>
                </div>
                <div class="card-body">
                    @if(request('pathway_id') && $stepAnalysis->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Day') }}</th>
                                        <th>{{ __('Activity') }}</th>
                                        <th>{{ __('Times Performed') }}</th>
                                        <th>{{ __('Compliance Rate') }}</th>
                                        <th>{{ __('Avg. Actual Cost') }}</th>
                                        <th>{{ __('Standard Cost') }}</th>
                                        <th>{{ __('Avg. Variance') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stepAnalysis as $step)
                                        <tr>
                                            <td>{{ $step->day }}</td>
                                            <td>{{ $step->activity }}</td>
                                            <td>{{ $step->times_performed }}</td>
                                            <td>
                                                <span class="{{ $step->compliance_rate >= 90 ? 'text-success' : ($step->compliance_rate >= 70 ? 'text-warning' : 'text-danger') }}">
                                                    {{ number_format($step->compliance_rate, 2) }}%
                                                </span>
                                            </td>
                                            <td>Rp{{ number_format($step->avg_actual_cost, 2) }}</td>
                                            <td>Rp{{ number_format($step->standard_cost, 2) }}</td>
                                            <td>
                                                <span class="{{ $step->avg_variance <= 0 ? 'text-success' : 'text-danger' }}">
                                                    Rp{{ number_format($step->avg_variance, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif(request('pathway_id'))
                        <p>{{ __('No step analysis data found for the selected pathway.') }}</p>
                    @else
                        <p>{{ __('Please select a specific pathway to view step analysis.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
