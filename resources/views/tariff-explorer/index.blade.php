@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Tariff Explorer') }}</h2>
        </div>
        
        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('tariff-explorer.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">{{ __('Search') }}</label>
                        <input type="text" id="search" name="search" value="{{ $search }}" placeholder="{{ __('Service code/name...') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                    </div>
                    
                    <div>
                        <label for="tariff_class_id" class="block text-sm font-medium text-gray-700">{{ __('Tariff Class') }}</label>
                        <select id="tariff_class_id" name="tariff_class_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                            <option value="">{{ __('All Classes') }}</option>
                            @foreach($tariffClasses as $tc)
                                <option value="{{ $tc->id }}" {{ $tariffClassId == $tc->id ? 'selected' : '' }}>{{ $tc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="effective_date" class="block text-sm font-medium text-gray-700">{{ __('Effective Date') }}</label>
                        <input type="date" id="effective_date" name="effective_date" value="{{ $effectiveDate }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                    </div>
                    
                    <div class="flex items-end">
                        <label class="flex items-center">
                            <input type="checkbox" name="show_expired" value="1" {{ $showExpired ? 'checked' : '' }} class="rounded border-gray-300 text-biru-dongker-800 focus:ring-biru-dongker-700">
                            <span class="ml-2 text-sm text-gray-700">{{ __('Show Expired') }}</span>
                        </label>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                            {{ __('Search') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($tariffs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Tariff Class') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Final Tariff Price') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Effective Date') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('SK Number') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($tariffs as $tariff)
                                    <tr>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">
                                            <div>
                                                <div class="font-medium">{{ $tariff->costReference->service_code ?? '-' }}</div>
                                                <div class="text-gray-500 text-xs">{{ \Illuminate\Support\Str::limit($tariff->costReference->service_description ?? '-', 50) }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ $tariff->tariffClass->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
                                            Rp {{ number_format($tariff->final_tariff_price, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
                                            <div>
                                                <div>{{ $tariff->effective_date->format('d/m/Y') }}</div>
                                                @if($tariff->expired_date)
                                                    <div class="text-xs text-gray-400">Exp: {{ $tariff->expired_date->format('d/m/Y') }}</div>
                                                @else
                                                    <div class="text-xs text-green-600">Active</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ $tariff->sk_number }}
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm">
                                            <a href="{{ route('tariff-explorer.show', $tariff) }}" class="text-biru-dongker-800 hover:text-biru-dongker-950">
                                                {{ __('View Details') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $tariffs->links() }}
                    </div>
                @else
                    <p class="text-gray-600">{{ __('No tariffs found.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

