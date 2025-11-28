@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Tariff Details') }}</h2>
            <a href="{{ route('tariff-explorer.index', request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('Back to Explorer') }}
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Tariff Information') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Service Code') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $finalTariff->costReference->service_code ?? '-' }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Service Description') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $finalTariff->costReference->service_description ?? '-' }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Tariff Class') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $finalTariff->tariffClass->name ?? '-' }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Final Tariff Price') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">Rp {{ number_format($finalTariff->final_tariff_price, 2, ',', '.') }}</dd>
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
                        <dt class="text-sm font-medium text-gray-500">{{ __('SK Number') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $finalTariff->sk_number }}</dd>
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
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        @if($inaCbgComparison && $inaCbgComparison['has_ina_cbg'])
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Comparison with INA-CBG') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('INA-CBG Tariff') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($inaCbgComparison['ina_cbg_tariff'], 2, ',', '.') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Internal Tariff') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($inaCbgComparison['internal_tariff'], 2, ',', '.') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Difference') }}</dt>
                        <dd class="mt-1 text-sm font-semibold {{ $inaCbgComparison['difference'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format($inaCbgComparison['difference'], 2, ',', '.') }}
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Difference %') }}</dt>
                        <dd class="mt-1 text-sm font-semibold {{ $inaCbgComparison['difference_percentage'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($inaCbgComparison['difference_percentage'], 2) }}%
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
        @endif

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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $h->effective_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($h->final_tariff_price, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $h->sk_number }}</td>
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

