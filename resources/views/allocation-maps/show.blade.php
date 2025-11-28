@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Allocation Map Details') }}</h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('allocation-maps.edit', $allocationMap) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('allocation-maps.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Allocation Map Information') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Step Sequence') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-biru-dongker-300 text-biru-dongker-900">
                                Step {{ $allocationMap->step_sequence }}
                            </span>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Source Cost Center') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div>
                                <div class="font-medium">{{ $allocationMap->sourceCostCenter->name ?? '-' }}</div>
                                <div class="text-gray-500 text-xs">{{ $allocationMap->sourceCostCenter->code ?? '-' }}</div>
                            </div>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Allocation Driver') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div>
                                <div class="font-medium">{{ $allocationMap->allocationDriver->name ?? '-' }}</div>
                                <div class="text-gray-500 text-xs">{{ $allocationMap->allocationDriver->unit_measurement ?? '-' }}</div>
                                @if($allocationMap->allocationDriver->description)
                                    <div class="text-gray-500 text-xs mt-1">{{ $allocationMap->allocationDriver->description }}</div>
                                @endif
                            </div>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Created At') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $allocationMap->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Updated At') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $allocationMap->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

