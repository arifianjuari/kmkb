@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('Audit Logs') }}</h2>
                <form action="{{ route('audit-logs.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('Are you sure you want to clear all audit logs? This action cannot be undone.') }}')">{{ __('Clear All Logs') }}</button>
                </form>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('audit-logs.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">{{ __('User') }}</label>
                                    <select class="form-control" id="user_id" name="user_id">
                                        <option value="">{{ __('All Users') }}</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="action" class="form-label">{{ __('Action') }}</label>
                                    <select class="form-control" id="action" name="action">
                                        <option value="">{{ __('All Actions') }}</option>
                                        <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>{{ __('Login') }}</option>
                                        <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>{{ __('Logout') }}</option>
                                        <option value="register" {{ request('action') == 'register' ? 'selected' : '' }}>{{ __('Register') }}</option>
                                        <option value="password_reset" {{ request('action') == 'password_reset' ? 'selected' : '' }}>{{ __('Password Reset') }}</option>
                                        <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>{{ __('Create') }}</option>
                                        <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>{{ __('Update') }}</option>
                                        <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>{{ __('Delete') }}</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="date_from" class="form-label">{{ __('Date From') }}</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="date_to" class="form-label">{{ __('Date To') }}</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">{{ __('Filter') }}</button>
                            <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">{{ __('Clear') }}</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    @if($auditLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('User') }}</th>
                                        <th>{{ __('Action') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('IP Address') }}</th>
                                        <th>{{ __('User Agent') }}</th>
                                        <th>{{ __('Timestamp') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($auditLogs as $log)
                                        <tr>
                                            <td>
                                                @if($log->user)
                                                    {{ $log->user->name }}
                                                @else
                                                    <span class="text-muted">{{ __('System') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->action == 'login')
                                                    <span class="badge bg-success">{{ __('Login') }}</span>
                                                @elseif($log->action == 'logout')
                                                    <span class="badge bg-secondary">{{ __('Logout') }}</span>
                                                @elseif($log->action == 'register')
                                                    <span class="badge bg-info">{{ __('Register') }}</span>
                                                @elseif($log->action == 'password_reset')
                                                    <span class="badge bg-warning">{{ __('Password Reset') }}</span>
                                                @elseif($log->action == 'create')
                                                    <span class="badge bg-primary">{{ __('Create') }}</span>
                                                @elseif($log->action == 'update')
                                                    <span class="badge bg-warning">{{ __('Update') }}</span>
                                                @elseif($log->action == 'delete')
                                                    <span class="badge bg-danger">{{ __('Delete') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $log->action }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $log->description }}</td>
                                            <td>{{ $log->ip_address }}</td>
                                            <td>{{ Str::limit($log->user_agent, 30) }}</td>
                                            <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                            <td>
                                                <a href="{{ route('audit-logs.show', $log) }}" class="btn btn-sm btn-info">{{ __('View') }}</a>
                                                
                                                <form action="{{ route('audit-logs.destroy', $log) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure you want to delete this log entry?') }}')">{{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{ $auditLogs->links() }}
                    @else
                        <p>{{ __('No audit logs found.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
