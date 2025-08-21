@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('Dashboard Summary Report') }}</h2>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">{{ __('Back to Reports') }}</a>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Report Filters') }}</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.dashboard') }}">
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
                            <a href="{{ route('reports.dashboard') }}" class="btn btn-secondary">{{ __('Clear') }}</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">{{ __('Total Cases') }}</h5>
                                    <h2>{{ $totalCases }}</h2>
                                </div>
                                <i class="fas fa-file-medical fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">{{ __('Avg. Compliance') }}</h5>
                                    <h2>{{ number_format($averageCompliance, 2) }}%</h2>
                                </div>
                                <i class="fas fa-percentage fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">{{ __('Total Charges') }}</h5>
                                    <h2>Rp{{ number_format($totalCharges, 2) }}</h2>
                                </div>
                                <i class="fas fa-money-bill-wave fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">{{ __('Total Variance') }}</h5>
                                    <h2>Rp{{ number_format($totalCostVariance, 2) }}</h2>
                                </div>
                                <i class="fas fa-chart-line fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('Cases by Pathway') }}</h5>
                        </div>
                        <div class="card-body">
                            @if($casesByPathway->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Pathway') }}</th>
                                                <th>{{ __('Cases') }}</th>
                                                <th>{{ __('Avg. Compliance') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($casesByPathway as $data)
                                                <tr>
                                                    <td>{{ $data->pathway_name }}</td>
                                                    <td>{{ $data->case_count }}</td>
                                                    <td>{{ number_format($data->avg_compliance, 2) }}%</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>{{ __('No data available.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('Monthly Trend') }}</h5>
                        </div>
                        <div class="card-body">
                            @if($monthlyTrend->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Month') }}</th>
                                                <th>{{ __('Cases') }}</th>
                                                <th>{{ __('Avg. Compliance') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monthlyTrend as $data)
                                                <tr>
                                                    <td>{{ $data->month }}</td>
                                                    <td>{{ $data->case_count }}</td>
                                                    <td>{{ number_format($data->avg_compliance, 2) }}%</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>{{ __('No data available.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
