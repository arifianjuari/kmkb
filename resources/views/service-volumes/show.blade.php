@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Service Volume Details') }}</h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('service-volumes.edit', $serviceVolume) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('service-volumes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Service Volume Information') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Period') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $serviceVolume->period_month }}/{{ $serviceVolume->period_year }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Service Code') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $serviceVolume->costReference ? $serviceVolume->costReference->service_code : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Service Description') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $serviceVolume->costReference ? $serviceVolume->costReference->service_description : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Tariff Class') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $serviceVolume->tariffClass ? $serviceVolume->tariffClass->name . ' (' . $serviceVolume->tariffClass->code . ')' : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Total Quantity') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ number_format($serviceVolume->total_quantity, 2, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Created At') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $serviceVolume->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

