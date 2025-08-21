@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>{{ __('Reports') }}</h2>
            <p>{{ __('Select a report type to view detailed analytics and insights.') }}</p>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">{{ __('Dashboard Summary') }}</h5>
                    <p class="card-text">{{ __('Overview of key metrics and performance indicators.') }}</p>
                    <a href="{{ route('reports.dashboard') }}" class="btn btn-primary">{{ __('View Report') }}</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-percentage fa-3x mb-3 text-success"></i>
                    <h5 class="card-title">{{ __('Compliance Report') }}</h5>
                    <p class="card-text">{{ __('Analysis of clinical pathway compliance rates.') }}</p>
                    <a href="{{ route('reports.compliance') }}" class="btn btn-success">{{ __('View Report') }}</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-money-bill-wave fa-3x mb-3 text-warning"></i>
                    <h5 class="card-title">{{ __('Cost Variance Report') }}</h5>
                    <p class="card-text">{{ __('Analysis of cost variations and financial performance.') }}</p>
                    <a href="{{ route('reports.cost-variance') }}" class="btn btn-warning">{{ __('View Report') }}</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-procedures fa-3x mb-3 text-info"></i>
                    <h5 class="card-title">{{ __('Pathway Performance') }}</h5>
                    <p class="card-text">{{ __('Performance metrics for clinical pathways.') }}</p>
                    <a href="{{ route('reports.pathway-performance') }}" class="btn btn-info">{{ __('View Report') }}</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-file-export fa-3x mb-3 text-secondary"></i>
                    <h5 class="card-title">{{ __('Export Data') }}</h5>
                    <p class="card-text">{{ __('Export reports and data in various formats.') }}</p>
                    <a href="{{ route('reports.export') }}" class="btn btn-secondary">{{ __('Export') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
