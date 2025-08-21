@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h4>{{ __('Welcome to KMKB System') }}</h4>
                    <p>{{ __('This system helps manage clinical pathways and control healthcare costs.') }}</p>
                    
                    <div class="row mt-4">
                        @if(Auth::user()->hasRole('mutu') || Auth::user()->hasRole('admin'))
                            <div class="col-md-4 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-procedures fa-3x mb-3"></i>
                                        <h5 class="card-title">{{ __('Pathways') }}</h5>
                                        <p class="card-text">{{ __('Manage clinical pathways') }}</p>
                                        <a href="{{ route('pathways.index') }}" class="btn btn-primary">{{ __('View') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if(Auth::user()->hasRole('klaim') || Auth::user()->hasRole('admin'))
                            <div class="col-md-4 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-file-medical fa-3x mb-3"></i>
                                        <h5 class="card-title">{{ __('Cases') }}</h5>
                                        <p class="card-text">{{ __('Manage patient cases') }}</p>
                                        <a href="{{ route('cases.index') }}" class="btn btn-primary">{{ __('View') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if(Auth::user()->hasRole('manajemen') || Auth::user()->hasRole('admin'))
                            <div class="col-md-4 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                        <h5 class="card-title">{{ __('Reports') }}</h5>
                                        <p class="card-text">{{ __('View reports and analytics') }}</p>
                                        <a href="{{ route('reports.index') }}" class="btn btn-primary">{{ __('View') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if(Auth::user()->hasRole('admin'))
                            <div class="col-md-4 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <h5 class="card-title">{{ __('Users') }}</h5>
                                        <p class="card-text">{{ __('Manage system users') }}</p>
                                        <a href="{{ route('users.index') }}" class="btn btn-primary">{{ __('View') }}</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                        <h5 class="card-title">{{ __('Audit Logs') }}</h5>
                                        <p class="card-text">{{ __('View system audit logs') }}</p>
                                        <a href="{{ route('audit-logs.index') }}" class="btn btn-primary">{{ __('View') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
