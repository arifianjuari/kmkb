@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Allocation Result Details') }}</h2>
            <a href="{{ route('allocation-results.index', ['year' => $allocationResult->period_year, 'month' => $allocationResult->period_month]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('Back to List') }}
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Allocation Result Information') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Period') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ str_pad($allocationResult->period_month, 2, '0', STR_PAD_LEFT) }}/{{ $allocationResult->period_year }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Allocation Step') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ ucfirst(str_replace('_', ' ', $allocationResult->allocation_step)) }}
                            </span>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Source Cost Center') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div>
                                <div class="font-medium">{{ $allocationResult->sourceCostCenter->name ?? '-' }}</div>
                                <div class="text-gray-500 text-xs">{{ $allocationResult->sourceCostCenter->code ?? '-' }}</div>
                            </div>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Target Cost Center') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div>
                                <div class="font-medium">{{ $allocationResult->targetCostCenter->name ?? '-' }}</div>
                                <div class="text-gray-500 text-xs">{{ $allocationResult->targetCostCenter->code ?? '-' }}</div>
                            </div>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Allocated Amount') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            Rp {{ number_format($allocationResult->allocated_amount, 2, ',', '.') }}
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Created At') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $allocationResult->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

