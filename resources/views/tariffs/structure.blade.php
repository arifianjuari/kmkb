@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Tariff Structure Setup') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('structure-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is Tariff Structure?') }}"
                    title="{{ __('What is Tariff Structure?') }}"
                >
                    i
                </button>
            </div>
            <a href="{{ route('tariffs.final') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('View Final Tariffs') }}
            </a>
        </div>

        <div id="structure-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Tariff Structure</span> menampilkan breakdown komponen tarif untuk setiap layanan. Struktur tarif terdiri dari:
            </p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li><strong>Base Unit Cost:</strong> Biaya dasar dari perhitungan unit cost</li>
                <li><strong>Margin:</strong> Persentase margin keuntungan (biasanya 5-20%)</li>
                <li><strong>Jasa Sarana:</strong> Komponen fasilitas/akomodasi (hoteling, ruangan, dll)</li>
                <li><strong>Jasa Pelayanan:</strong> Komponen profesional (dokter, perawat, dll)</li>
                <li><strong>Final Tariff Price:</strong> Total harga tarif = Base + Margin + Jasa Sarana + Jasa Pelayanan</li>
            </ul>
        </div>

        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-4 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Total Tariffs') }}</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($summaryStats['total_tariffs'], 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Active Tariffs') }}</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($summaryStats['active_tariffs'], 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Avg Margin') }}</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($summaryStats['avg_margin'] * 100 ?? 0, 2) }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-purple-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Avg Final Price') }}</dt>
                                <dd class="text-lg font-medium text-gray-900">Rp {{ number_format($componentStats->avg_final_price ?? 0, 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Component Breakdown Statistics -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Component Breakdown (Active Tariffs)') }}</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">{{ __('Base Unit Cost') }}</p>
                        <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($componentStats->avg_base_unit_cost ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Average') }}</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">{{ __('Margin') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ number_format($componentStats->avg_margin_percent ?? 0, 2) }}%</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Average') }}</p>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">{{ __('Jasa Sarana') }}</p>
                        <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($componentStats->avg_jasa_sarana ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Average') }}</p>
                    </div>
                    <div class="text-center p-4 bg-orange-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">{{ __('Jasa Pelayanan') }}</p>
                        <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($componentStats->avg_jasa_pelayanan ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Average') }}</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">{{ __('Final Price') }}</p>
                        <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($componentStats->avg_final_price ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Average') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown by Tariff Class -->
        @if($breakdownByClass->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Breakdown by Tariff Class') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Tariff Class') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Count') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Avg Margin') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Avg Jasa Sarana') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Avg Jasa Pelayanan') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($breakdownByClass as $class)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $class->code }} - {{ $class->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ number_format($class->count, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ number_format($class->avg_margin, 2) }}%
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        Rp {{ number_format($class->avg_jasa_sarana, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        Rp {{ number_format($class->avg_jasa_pelayanan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Filters -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('tariffs.structure') }}" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div>
                            <label for="tariff_class_id" class="block text-sm font-medium text-gray-700">{{ __('Tariff Class') }}</label>
                            <select id="tariff_class_id" name="tariff_class_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                                <option value="">{{ __('All Classes') }}</option>
                                @foreach($tariffClasses as $tc)
                                    <option value="{{ $tc->id }}" {{ $tariffClassId == $tc->id ? 'selected' : '' }}>
                                        {{ $tc->code }} - {{ $tc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                            <select id="status" name="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="expired" {{ $status == 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">{{ __('Search') }}</label>
                            <input type="text" id="search" name="search" value="{{ $search }}" placeholder="{{ __('Service code/name...') }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                                {{ __('Filter') }}
                            </button>
                        </div>
                    </div>
                    
                    @if($tariffClassId || $search || $status != 'active')
                        <div>
                            <a href="{{ route('tariffs.structure') }}" class="text-sm text-biru-dongker-800 hover:text-biru-dongker-900">
                                {{ __('Clear Filters') }}
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Tariff Structure Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Tariff Structure Details') }}</h3>
                
                @if($tariffs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Tariff Class') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Base Unit Cost') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Margin') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Jasa Sarana') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Jasa Pelayanan') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Final Price') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Effective Date') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('SK Number') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($tariffs as $tariff)
                                    <tr class="{{ !$tariff->isActive() ? 'bg-gray-50' : '' }}">
                                        <td class="px-6 py-4 text-sm">
                                            <div class="font-medium text-gray-900">{{ $tariff->costReference->service_code ?? '-' }}</div>
                                            <div class="text-gray-500 text-xs">{{ Str::limit($tariff->costReference->service_description ?? '-', 40) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $tariff->tariffClass ? $tariff->tariffClass->code . ' - ' . $tariff->tariffClass->name : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            Rp {{ number_format($tariff->base_unit_cost, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            {{ number_format($tariff->margin_percentage * 100, 2) }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            Rp {{ number_format($tariff->jasa_sarana, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            Rp {{ number_format($tariff->jasa_pelayanan, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
                                            Rp {{ number_format($tariff->final_tariff_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $tariff->effective_date ? \Carbon\Carbon::parse($tariff->effective_date)->locale('id')->isoFormat('DD MMM YYYY') : '-' }}
                                            @if($tariff->expired_date)
                                                <br><span class="text-xs text-gray-400">s/d {{ \Carbon\Carbon::parse($tariff->expired_date)->locale('id')->isoFormat('DD MMM YYYY') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $tariff->sk_number }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $tariffs->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No tariff structures found') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('No matching tariff structures found for the selected filters.') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
