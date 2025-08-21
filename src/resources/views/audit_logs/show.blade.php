@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('Audit Log Details') }}</h2>
                <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">{{ __('Back to List') }}</a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('User') }}</th>
                                    <td>
                                        @if($auditLog->user)
                                            <a href="{{ route('users.show', $auditLog->user) }}">{{ $auditLog->user->name }}</a>
                                        @else
                                            <span class="text-muted">{{ __('System') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Action') }}</th>
                                    <td>
                                        @if($auditLog->action == 'login')
                                            <span class="badge bg-success">{{ __('Login') }}</span>
                                        @elseif($auditLog->action == 'logout')
                                            <span class="badge bg-secondary">{{ __('Logout') }}</span>
                                        @elseif($auditLog->action == 'register')
                                            <span class="badge bg-info">{{ __('Register') }}</span>
                                        @elseif($auditLog->action == 'password_reset')
                                            <span class="badge bg-warning">{{ __('Password Reset') }}</span>
                                        @elseif($auditLog->action == 'create')
                                            <span class="badge bg-primary">{{ __('Create') }}</span>
                                        @elseif($auditLog->action == 'update')
                                            <span class="badge bg-warning">{{ __('Update') }}</span>
                                        @elseif($auditLog->action == 'delete')
                                            <span class="badge bg-danger">{{ __('Delete') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $auditLog->action }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Description') }}</th>
                                    <td>{{ $auditLog->description }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('IP Address') }}</th>
                                    <td>{{ $auditLog->ip_address }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('User Agent') }}</th>
                                    <td>{{ $auditLog->user_agent }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Timestamp') }}</th>
                                    <td>{{ $auditLog->created_at->format('d M Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($auditLog->metadata)
                        <div class="mb-3">
                            <label class="form-label"><strong>{{ __('Metadata') }}</strong></label>
                            <pre class="bg-light p-3">{{ json_encode($auditLog->metadata, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
