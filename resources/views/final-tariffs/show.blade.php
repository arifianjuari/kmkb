@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Final Tariff Details') }}</h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('final-tariffs.edit', $finalTariff) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('final-tariffs.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Tariff Information') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Service') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div>
                                <div class="font-medium">{{ $finalTariff->costReference->service_code ?? '-' }}</div>
                                <div class="text-gray-500 text-xs">{{ $finalTariff->costReference->service_description ?? '-' }}</div>
                            </div>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Tariff Class') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $finalTariff->tariffClass->name ?? '-' }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('SK Number') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $finalTariff->sk_number }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Base Unit Cost') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($finalTariff->base_unit_cost, 2, ',', '.') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Margin Percentage') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($finalTariff->margin_percentage * 100, 2) }}%</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Jasa Sarana') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($finalTariff->jasa_sarana, 2, ',', '.') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Jasa Pelayanan') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($finalTariff->jasa_pelayanan, 2, ',', '.') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Final Tariff Price') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">Rp {{ number_format($finalTariff->final_tariff_price, 2, ',', '.') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Effective Date') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $finalTariff->effective_date->format('d/m/Y') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Expired Date') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $finalTariff->expired_date ? $finalTariff->expired_date->format('d/m/Y') : '-' }}
                            @if($finalTariff->isActive())
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Expired</span>
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Unit Cost Version') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $finalTariff->unitCostCalculation->version_label ?? '-' }} ({{ $finalTariff->unitCostCalculation->period_month ?? '-' }}/{{ $finalTariff->unitCostCalculation->period_year ?? '-' }})</dd>
                    </div>
                </dl>
            </div>
        </div>

        @if($history->count() > 1)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Tariff History') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Effective Date') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Final Tariff Price') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('SK Number') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($history as $h)
                                <tr class="{{ $h->id == $finalTariff->id ? 'bg-biru-dongker-200' : '' }}">
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{{ $h->effective_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($h->final_tariff_price, 2, ',', '.') }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $h->sk_number }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

