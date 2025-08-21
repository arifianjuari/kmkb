@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>{{ __('Dashboard') }}</h2>
            <p>{{ __('Welcome to the KMKB Dashboard. Here you can find an overview of the system statistics and recent activities.') }}</p>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">{{ __('Total Pathways') }}</h5>
                            <h2>{{ $totalPathways }}</h2>
                        </div>
                        <i class="fas fa-procedures fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
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
            <div class="card text-white bg-info">
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
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">{{ __('Total Variance') }}</h5>
                            <h2>Rp{{ number_format($totalCostVariance, 2) }}</h2>
                        </div>
                        <i class="fas fa-money-bill-wave fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Recent Cases') }}</h5>
                </div>
                <div class="card-body">
                    @if($recentCases->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('MRN') }}</th>
                                        <th>{{ __('Pathway') }}</th>
                                        <th>{{ __('Admission Date') }}</th>
                                        <th>{{ __('Compliance') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentCases as $case)
                                        <tr>
                                            <td>{{ $case->medical_record_number }}</td>
                                            <td>{{ $case->clinicalPathway->name }}</td>
                                            <td>{{ $case->admission_date->format('d M Y') }}</td>
                                            <td>{{ number_format($case->compliance_percentage, 2) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>{{ __('No recent cases found.') }}</p>
                    @endif
                </div>
            </div>
        </div>
        
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($casesByPathway as $data)
                                        <tr>
                                            <td>{{ $data->pathway_name }}</td>
                                            <td>{{ $data->case_count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>{{ __('No cases found.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
