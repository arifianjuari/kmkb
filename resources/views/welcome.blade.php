@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-900">{{ __('Welcome to KMKB') }}</h1>
        </div>

        <div class="p-6">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">{{ __('Kendali Mutu Kendali Biaya') }}</h2>
                <p class="mt-1 text-gray-600">{{ __('Clinical Pathway Based Quality and Cost Control System') }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('About KMKB') }}</h3>
                    <p class="mt-2 text-gray-700">{{ __('The KMKB system is designed to help healthcare institutions implement clinical pathways for standardized patient care while controlling costs through INA-CBGs (Indonesian Case Based Groups) methodology.') }}</p>

                    <h3 class="mt-6 text-lg font-semibold text-gray-900">{{ __('Key Features') }}</h3>
                    <ul class="mt-2 list-disc list-inside space-y-1 text-gray-700">
                        <li>{{ __('Clinical Pathway Management') }}</li>
                        <li>{{ __('Patient Case Tracking') }}</li>
                        <li>{{ __('Cost Variance Analysis') }}</li>
                        <li>{{ __('Compliance Monitoring') }}</li>
                        <li>{{ __('Reporting and Analytics') }}</li>
                        <li>{{ __('Audit Trail') }}</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Get Started') }}</h3>
                    @if (Route::has('login'))
                        <div class="mt-3 flex flex-col sm:flex-row sm:items-center gap-3">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    {{ __('Dashboard') }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    {{ __('Login') }}
                                </a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-900 border border-gray-300 text-sm font-medium rounded-md shadow hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        {{ __('Register') }}
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif

                    <div class="mt-6">
                        <h4 class="text-base font-semibold text-gray-900">{{ __('User Roles') }}</h4>
                        <ul class="mt-2 space-y-1 text-gray-700">
                            <li><span class="font-semibold">{{ __('Admin') }}</span>: {{ __('Full system access') }}</li>
                            <li><span class="font-semibold">{{ __('Mutu') }}</span>: {{ __('Pathway management') }}</li>
                            <li><span class="font-semibold">{{ __('Klaim') }}</span>: {{ __('Case management') }}</li>
                            <li><span class="font-semibold">{{ __('Manajemen') }}</span>: {{ __('Reporting and analytics') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
