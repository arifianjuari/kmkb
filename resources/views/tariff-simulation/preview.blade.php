@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Tariff Simulation Preview') }}</h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('tariff-simulation.export', request()->all()) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    {{ __('Export Excel') }}
                </a>
                <a href="{{ route('tariff-simulation.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    {{ __('Back to Simulation') }}
                </a>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <p class="text-sm text-gray-500">Version</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $versionLabel }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Global Margin</p>
                        <p class="text-lg font-semibold text-gray-900">{{ number_format($globalMargin * 100, 2) }}%</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Services</p>
                        <p class="text-lg font-semibold text-gray-900">{{ count($results) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Revenue</p>
                        <p class="text-lg font-semibold text-gray-900">Rp {{ number_format(array_sum(array_column($results, 'final_tariff_price')), 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if(count($results) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service Code') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service Description') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Base Unit Cost') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Margin %') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Margin Amount') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Jasa Sarana') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Jasa Pelayanan') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Final Tariff Price') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($results as $result)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $result['service_code'] }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $result['service_description'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($result['base_unit_cost'], 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($result['margin_percentage'] * 100, 2) }}%</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($result['margin_amount'], 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($result['jasa_sarana'], 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($result['jasa_pelayanan'], 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">Rp {{ number_format($result['final_tariff_price'], 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('final-tariffs.create', [
                                                'cost_reference_id' => $result['cost_reference_id'],
                                                'unit_cost_calculation_id' => $result['unit_cost_calculation_id'],
                                                'base_unit_cost' => $result['base_unit_cost'],
                                                'margin_percentage' => $result['margin_percentage'],
                                                'jasa_sarana' => $result['jasa_sarana'],
                                                'jasa_pelayanan' => $result['jasa_pelayanan'],
                                                'final_tariff_price' => $result['final_tariff_price'],
                                            ]) }}" class="text-biru-dongker-800 hover:text-biru-dongker-950">
                                                {{ __('Create Final Tariff') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-600">{{ __('No results found.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

