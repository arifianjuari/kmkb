@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Final Tariffs') }}</h2>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('final-tariffs.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    {{ __('Export Excel') }}
                </a>
                <a href="{{ route('final-tariffs.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                    {{ __('Add New Final Tariff') }}
                </a>
            </div>
        </div>
        


        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('final-tariffs.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-6">
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
                        <label for="sk_number" class="block text-sm font-medium text-gray-700">{{ __('SK Number') }}</label>
                        <input type="text" id="sk_number" name="sk_number" value="{{ $skNumber }}" placeholder="{{ __('SK...') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                    </div>
                    
                    <div>
                        <label for="effective_date_from" class="block text-sm font-medium text-gray-700">{{ __('Effective From') }}</label>
                        <input type="date" id="effective_date_from" name="effective_date_from" value="{{ $effectiveDateFrom }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                    </div>
                    
                    <div>
                        <label for="effective_date_to" class="block text-sm font-medium text-gray-700">{{ __('Effective To') }}</label>
                        <input type="date" id="effective_date_to" name="effective_date_to" value="{{ $effectiveDateTo }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                    </div>
                    
                    <div class="flex items-end">
                        <div class="flex items-center space-x-2 w-full">
                            <label class="flex items-center">
                                <input type="checkbox" name="show_expired" value="1" {{ $showExpired ? 'checked' : '' }} class="rounded border-gray-300 text-biru-dongker-800 focus:ring-biru-dongker-700">
                                <span class="ml-2 text-sm text-gray-700">{{ __('Show Expired') }}</span>
                            </label>
                            <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                                {{ __('Filter') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($finalTariffs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Tariff Class') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Base Unit Cost') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Margin %') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Final Tariff Price') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('SK Number') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Effective Date') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($finalTariffs as $tariff)
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
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                                            Rp {{ number_format($tariff->base_unit_cost, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                                            {{ number_format($tariff->margin_percentage * 100, 2) }}%
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
                                            Rp {{ number_format($tariff->final_tariff_price, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ $tariff->sk_number }}
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
                                        <td class="px-6 py-2 whitespace-nowrap text-sm">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('final-tariffs.show', $tariff) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('View') }}" aria-label="{{ __('View') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('final-tariffs.edit', $tariff) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-biru-dongker-800 hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('final-tariffs.destroy', $tariff) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this final tariff?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                            <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1Zm-3 6h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 9Zm3 2v8h2v-8H9Zm4 0v8h2v-8h-2Z"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $finalTariffs->links() }}
                    </div>
                @else
                    <p class="text-gray-600">{{ __('No final tariffs found.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

