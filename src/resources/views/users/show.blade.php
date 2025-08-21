@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>{{ __('User Details') }}</h2>
                <div>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('Back to List') }}</a>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">{{ __('Edit') }}</a>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('User Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Email') }}</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Role') }}</th>
                                    <td>
                                        @if($user->hasRole('admin'))
                                            <span class="badge bg-danger">{{ __('Admin') }}</span>
                                        @elseif($user->hasRole('mutu'))
                                            <span class="badge bg-primary">{{ __('Mutu') }}</span>
                                        @elseif($user->hasRole('klaim'))
                                            <span class="badge bg-success">{{ __('Klaim') }}</span>
                                        @elseif($user->hasRole('manajemen'))
                                            <span class="badge bg-warning">{{ __('Manajemen') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('Department') }}</th>
                                    <td>{{ $user->department }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Created At') }}</th>
                                    <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Last Updated') }}</th>
                                    <td>{{ $user->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Recent Activity') }}</h5>
                </div>
                <div class="card-body">
                    @if($user->auditLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Action') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('IP Address') }}</th>
                                        <th>{{ __('Timestamp') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->auditLogs->take(10) as $log)
                                        <tr>
                                            <td>{{ $log->action }}</td>
                                            <td>{{ $log->description }}</td>
                                            <td>{{ $log->ip_address }}</td>
                                            <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>{{ __('No recent activity found for this user.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
