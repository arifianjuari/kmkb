@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('Patient Case Details') }}</h2>
                <div>
                    <a href="{{ route('cases.index') }}" class="btn btn-secondary">{{ __('Back to List') }}</a>
                    <a href="{{ route('cases.edit', $case) }}" class="btn btn-primary">{{ __('Edit') }}</a>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Case Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('Medical Record Number') }}</th>
                                    <td>{{ $case->medical_record_number }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Patient Name') }}</th>
                                    <td>{{ $case->patient_name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Clinical Pathway') }}</th>
                                    <td>{{ $case->clinicalPathway->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Primary Diagnosis') }}</th>
                                    <td>{{ $case->primary_diagnosis }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('Admission Date') }}</th>
                                    <td>{{ $case->admission_date->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Discharge Date') }}</th>
                                    <td>
                                        @if($case->discharge_date)
                                            {{ $case->discharge_date->format('d M Y') }}
                                        @else
                                            <span class="text-muted">{{ __('Not Discharged') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Compliance Percentage') }}</th>
                                    <td>
                                        @if($case->compliance_percentage !== null)
                                            <span class="{{ $case->compliance_percentage >= 90 ? 'text-success' : ($case->compliance_percentage >= 70 ? 'text-warning' : 'text-danger') }}">
                                                {{ number_format($case->compliance_percentage, 2) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">{{ __('N/A') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Cost Variance') }}</th>
                                    <td>
                                        @if($case->cost_variance !== null)
                                            <span class="{{ $case->cost_variance <= 0 ? 'text-success' : 'text-danger' }}">
                                                Rp{{ number_format($case->cost_variance, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">{{ __('N/A') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($case->additional_diagnoses)
                        <div class="mb-3">
                            <label class="form-label"><strong>{{ __('Additional Diagnoses') }}</strong></label>
                            <p>{{ $case->additional_diagnoses }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Financial Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('Total Charges') }}</th>
                                    <td>Rp{{ number_format($case->total_charges, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Insurance Payment') }}</th>
                                    <td>Rp{{ number_format($case->insurance_payment, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('Patient Payment') }}</th>
                                    <td>Rp{{ number_format($case->patient_payment, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Outstanding Balance') }}</th>
                                    <td>Rp{{ number_format($case->outstanding_balance, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Case Details') }}</h5>
                </div>
                <div class="card-body">
                    @if($case->caseDetails->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Day') }}</th>
                                        <th>{{ __('Activity') }}</th>
                                        <th>{{ __('Performed') }}</th>
                                        <th>{{ __('Actual Cost') }}</th>
                                        <th>{{ __('Standard Cost') }}</th>
                                        <th>{{ __('Variance') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($case->caseDetails as $detail)
                                        <tr>
                                            <td>{{ $detail->pathwayStep->day }}</td>
                                            <td>{{ $detail->pathwayStep->activity }}</td>
                                            <td>
                                                @if($detail->performed)
                                                    <span class="badge bg-success">{{ __('Yes') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('No') }}</span>
                                                @endif
                                            </td>
                                            <td>Rp{{ number_format($detail->actual_cost, 2) }}</td>
                                            <td>Rp{{ number_format($detail->pathwayStep->standard_cost, 2) }}</td>
                                            <td>
                                                @if($detail->cost_variance !== null)
                                                    <span class="{{ $detail->cost_variance <= 0 ? 'text-success' : 'text-danger' }}">
                                                        Rp{{ number_format($detail->cost_variance, 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">{{ __('N/A') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>{{ __('No case details recorded yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
