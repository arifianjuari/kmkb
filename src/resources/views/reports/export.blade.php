@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('Export Data') }}</h2>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">{{ __('Back to Reports') }}</a>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> {{ __('Select the data you want to export and choose your preferred format. Exports will be generated and available for download shortly.') }}
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Export Options') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('reports.export.generate') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label"><strong>{{ __('Data to Export') }}</strong></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="export_cases" name="export_data[]" value="cases" checked>
                                        <label class="form-check-label" for="export_cases">
                                            {{ __('Patient Cases') }}
                                            <small class="text-muted d-block">{{ __('All patient case records with details') }}</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="export_pathways" name="export_data[]" value="pathways">
                                        <label class="form-check-label" for="export_pathways">
                                            {{ __('Clinical Pathways') }}
                                            <small class="text-muted d-block">{{ __('All clinical pathway definitions and steps') }}</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="export_users" name="export_data[]" value="users">
                                        <label class="form-check-label" for="export_users">
                                            {{ __('Users') }}
                                            <small class="text-muted d-block">{{ __('User accounts and roles') }}</small>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="export_audit_logs" name="export_data[]" value="audit_logs">
                                        <label class="form-check-label" for="export_audit_logs">
                                            {{ __('Audit Logs') }}
                                            <small class="text-muted d-block">{{ __('System activity and user actions') }}</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="export_compliance" name="export_data[]" value="compliance">
                                        <label class="form-check-label" for="export_compliance">
                                            {{ __('Compliance Reports') }}
                                            <small class="text-muted d-block">{{ __('Compliance metrics and analysis') }}</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="export_cost_variance" name="export_data[]" value="cost_variance">
                                        <label class="form-check-label" for="export_cost_variance">
                                            {{ __('Cost Variance Reports') }}
                                            <small class="text-muted d-block">{{ __('Financial performance data') }}</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label"><strong>{{ __('Export Format') }}</strong></label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" id="format_csv" name="format" value="csv" checked>
                                        <label class="form-check-label" for="format_csv">
                                            {{ __('CSV') }}
                                            <small class="text-muted d-block">{{ __('Comma-separated values') }}</small>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" id="format_excel" name="format" value="excel">
                                        <label class="form-check-label" for="format_excel">
                                            {{ __('Excel') }}
                                            <small class="text-muted d-block">{{ __('Microsoft Excel format') }}</small>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" id="format_pdf" name="format" value="pdf">
                                        <label class="form-check-label" for="format_pdf">
                                            {{ __('PDF') }}
                                            <small class="text-muted d-block">{{ __('Portable Document Format') }}</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label"><strong>{{ __('Date Range') }}</strong></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_from" class="form-label">{{ __('From') }}</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ now()->subMonth()->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_to" class="form-label">{{ __('To') }}</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-export"></i> {{ __('Generate Export') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Recent Exports') }}</h5>
                </div>
                <div class="card-body">
                    @if($recentExports->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('File Name') }}</th>
                                        <th>{{ __('Data Type') }}</th>
                                        <th>{{ __('Format') }}</th>
                                        <th>{{ __('Generated At') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentExports as $export)
                                        <tr>
                                            <td>{{ $export->file_name }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $export->data_type)) }}</td>
                                            <td>
                                                @if($export->format == 'csv')
                                                    <span class="badge bg-info">CSV</span>
                                                @elseif($export->format == 'excel')
                                                    <span class="badge bg-success">Excel</span>
                                                @elseif($export->format == 'pdf')
                                                    <span class="badge bg-danger">PDF</span>
                                                @endif
                                            </td>
                                            <td>{{ $export->created_at->format('d M Y H:i') }}</td>
                                            <td>
                                                @if($export->status == 'completed')
                                                    <span class="badge bg-success">{{ __('Completed') }}</span>
                                                @elseif($export->status == 'processing')
                                                    <span class="badge bg-warning">{{ __('Processing') }}</span>
                                                @elseif($export->status == 'failed')
                                                    <span class="badge bg-danger">{{ __('Failed') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($export->status == 'completed' && $export->file_path)
                                                    <a href="{{ route('reports.export.download', $export) }}" class="btn btn-sm btn-success">
                                                        <i class="fas fa-download"></i> {{ __('Download') }}
                                                    </a>
                                                @elseif($export->status == 'failed')
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        <i class="fas fa-exclamation-circle"></i> {{ __('Failed') }}
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        <i class="fas fa-clock"></i> {{ __('Processing') }}
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>{{ __('No recent exports found.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
