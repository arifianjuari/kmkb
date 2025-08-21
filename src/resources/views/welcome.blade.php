@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Welcome to KMKB') }}</div>

                <div class="card-body">
                    <div class="text-center mb-4">
                        <h1>{{ __('Kendali Mutu Kendali Biaya') }}</h1>
                        <p class="lead">{{ __('Clinical Pathway Based Quality and Cost Control System') }}</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h4>{{ __('About KMKB') }}</h4>
                            <p>{{ __('The KMKB system is designed to help healthcare institutions implement clinical pathways for standardized patient care while controlling costs through INA-CBGs (Indonesian Case Based Groups) methodology.') }}</p>
                            
                            <h4>{{ __('Key Features') }}</h4>
                            <ul>
                                <li>{{ __('Clinical Pathway Management') }}</li>
                                <li>{{ __('Patient Case Tracking') }}</li>
                                <li>{{ __('Cost Variance Analysis') }}</li>
                                <li>{{ __('Compliance Monitoring') }}</li>
                                <li>{{ __('Reporting and Analytics') }}</li>
                                <li>{{ __('Audit Trail') }}</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h4>{{ __('Get Started') }}</h4>
                            @if (Route::has('login'))
                                <div class="d-grid gap-2">
                                    @auth
                                        <a href="{{ url('/home') }}" class="btn btn-primary">{{ __('Dashboard') }}</a>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-primary">{{ __('Login') }}</a>

                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}" class="btn btn-secondary">{{ __('Register') }}</a>
                                        @endif
                                    @endauth
                                </div>
                            @endif
                            
                            <div class="mt-4">
                                <h5>{{ __('User Roles') }}</h5>
                                <ul>
                                    <li><strong>{{ __('Admin') }}</strong>: {{ __('Full system access') }}</li>
                                    <li><strong>{{ __('Mutu') }}</strong>: {{ __('Pathway management') }}</li>
                                    <li><strong>{{ __('Klaim') }}</strong>: {{ __('Case management') }}</li>
                                    <li><strong>{{ __('Manajemen') }}</strong>: {{ __('Reporting and analytics') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
