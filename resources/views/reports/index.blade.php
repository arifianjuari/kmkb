@extends('layouts.app')

@section('content')
<div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Reports') }}</h2>
            <p class="mt-1 text-gray-600">{{ __('Select a report type to view detailed analytics and insights.') }}</p>
        </div>
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100">
                        <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h5 class="mt-4 text-lg font-medium text-gray-900">{{ __('Dashboard Summary') }}</h5>
                    <p class="mt-2 text-gray-600">{{ __('Overview of key metrics and performance indicators.') }}</p>
                    <a href="{{ route('reports.dashboard') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('View Report') }}
                    </a>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                        <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h5 class="mt-4 text-lg font-medium text-gray-900">{{ __('Compliance Report') }}</h5>
                    <p class="mt-2 text-gray-600">{{ __('Analysis of clinical pathway compliance rates.') }}</p>
                    <a href="{{ route('reports.compliance') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        {{ __('View Report') }}
                    </a>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h5 class="mt-4 text-lg font-medium text-gray-900">{{ __('Cost Variance Report') }}</h5>
                    <p class="mt-2 text-gray-600">{{ __('Analysis of cost variations and financial performance.') }}</p>
                    <a href="{{ route('reports.cost-variance') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        {{ __('View Report') }}
                    </a>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-biru-dongker-300">
                        <svg class="h-8 w-8 text-biru-dongker-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                    <h5 class="mt-4 text-lg font-medium text-gray-900">{{ __('Pathway Performance') }}</h5>
                    <p class="mt-2 text-gray-600">{{ __('Performance metrics for clinical pathways.') }}</p>
                    <a href="{{ route('reports.pathway-performance') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-biru-dongker-800 text-white text-sm font-medium rounded-md hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                        {{ __('View Report') }}
                    </a>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100">
                        <svg class="h-8 w-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                    <h5 class="mt-4 text-lg font-medium text-gray-900">{{ __('Export Data') }}</h5>
                    <p class="mt-2 text-gray-600">{{ __('Export reports and data in various formats.') }}</p>
                    <a href="{{ route('reports.export') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        {{ __('Export') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
