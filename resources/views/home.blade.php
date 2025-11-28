@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mx-auto">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-xl font-semibold text-gray-900">{{ __('Dashboard') }}</h1>
            </div>

            <div class="p-6">
                    @if (session('status'))
                        <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-800" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h4 class="text-lg font-medium text-gray-900">{{ __('Welcome to KMKB System') }}</h4>
                    <p class="mt-1 text-gray-600">{{ __('This system helps manage clinical pathways and control healthcare costs.') }}</p>
                    
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @auth
                        @if(auth()->user()?->hasRole('mutu') || auth()->user()?->hasRole('admin'))
                            <div class="bg-white border border-gray-200 rounded-lg p-6 text-center shadow-sm">
                                <div class="text-3xl mb-3">🧭</div>
                                <h5 class="text-base font-semibold text-gray-900">{{ __('Pathways') }}</h5>
                                <p class="mt-1 text-sm text-gray-600">{{ __('Manage clinical pathways') }}</p>
                                <a href="{{ route('pathways.index') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-biru-dongker-800 text-white text-sm font-medium rounded-md shadow hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 focus:ring-offset-2">{{ __('View') }}</a>
                            </div>
                        @endif
                        
                        @if(auth()->user()?->hasRole('klaim') || auth()->user()?->hasRole('admin'))
                            <div class="bg-white border border-gray-200 rounded-lg p-6 text-center shadow-sm">
                                <div class="text-3xl mb-3">📄</div>
                                <h5 class="text-base font-semibold text-gray-900">{{ __('Cases') }}</h5>
                                <p class="mt-1 text-sm text-gray-600">{{ __('Manage patient cases') }}</p>
                                <a href="{{ route('cases.index') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-biru-dongker-800 text-white text-sm font-medium rounded-md shadow hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 focus:ring-offset-2">{{ __('View') }}</a>
                            </div>
                        @endif
                        
                        @if(auth()->user()?->hasRole('manajemen') || auth()->user()?->hasRole('admin'))
                            <div class="bg-white border border-gray-200 rounded-lg p-6 text-center shadow-sm">
                                <div class="text-3xl mb-3">📊</div>
                                <h5 class="text-base font-semibold text-gray-900">{{ __('Reports') }}</h5>
                                <p class="mt-1 text-sm text-gray-600">{{ __('View reports and analytics') }}</p>
                                <a href="{{ route('reports.index') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-biru-dongker-800 text-white text-sm font-medium rounded-md shadow hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 focus:ring-offset-2">{{ __('View') }}</a>
                            </div>
                        @endif
                        
                        @if(auth()->user()?->hasRole('admin'))
                            <div class="bg-white border border-gray-200 rounded-lg p-6 text-center shadow-sm">
                                <div class="text-3xl mb-3">👥</div>
                                <h5 class="text-base font-semibold text-gray-900">{{ __('Users') }}</h5>
                                <p class="mt-1 text-sm text-gray-600">{{ __('Manage system users') }}</p>
                                <a href="{{ route('users.index') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-biru-dongker-800 text-white text-sm font-medium rounded-md shadow hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 focus:ring-offset-2">{{ __('View') }}</a>
                            </div>
                            
                            <div class="bg-white border border-gray-200 rounded-lg p-6 text-center shadow-sm">
                                <div class="text-3xl mb-3">📝</div>
                                <h5 class="text-base font-semibold text-gray-900">{{ __('Audit Logs') }}</h5>
                                <p class="mt-1 text-sm text-gray-600">{{ __('View system audit logs') }}</p>
                                <a href="{{ route('audit-logs.index') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-biru-dongker-800 text-white text-sm font-medium rounded-md shadow hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 focus:ring-offset-2">{{ __('View') }}</a>
                            </div>
                        @endif
                        @endauth
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection
