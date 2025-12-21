@extends('layouts.app')

@section('content')
<section class="mx-auto py-6 sm:px-6 lg:px-8">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white whitespace-nowrap">{{ __('Base Tariff Reference') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors dark:text-biru-dongker-600 dark:border-biru-dongker-800 dark:hover:bg-biru-dongker-900"
                    onclick="const p = document.getElementById('base-tariff-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is Base Tariff?') }}"
                    title="{{ __('What is Base Tariff?') }}"
                >
                    i
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('jkn-cbg-codes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    {{ __('Manage CBG Codes') }}
                </a>
            </div>
        </div>
        
        <div id="base-tariff-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3 dark:bg-biru-dongker-900 dark:border-biru-dongker-900 dark:text-biru-dongker-300">
            <p class="mb-2">
                <span class="font-semibold">Base Tariff Reference</span> adalah referensi tarif dasar INA-CBG (JKN) untuk setiap kode CBG. Halaman ini menampilkan tarif paket INA-CBG yang digunakan sebagai acuan perbandingan dengan tarif internal rumah sakit.
            </p>
            <div class="mb-2">
                <p class="font-semibold mb-1">Fungsi utama:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Melihat tarif dasar INA-CBG per kode CBG</li>
                    <li>Membandingkan dengan tarif internal rumah sakit</li>
                    <li>Analisis variance antara tarif INA-CBG dan tarif RS</li>
                </ul>
            </div>
            <div>
                <p class="font-semibold mb-1">Peran di sistem:</p>
                <p class="ml-2">Sebagai referensi untuk:</p>
                <ul class="list-disc list-inside space-y-1 ml-4">
                    <li>Tariff Comparison (perbandingan tarif RS vs INA-CBG)</li>
                    <li>Case Variance Analysis (analisis selisih biaya kasus)</li>
                    <li>Pathway Performance Analysis (analisis performa pathway)</li>
                </ul>
            </div>
        </div>



        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('Total CBG Codes') }}</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($totalCodes) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('Active Codes') }}</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($activeCodes) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('Total Tariff Value') }}</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-white">Rp {{ number_format($totalTariff, 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('Average Tariff') }}</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-white">Rp {{ number_format($avgTariff ?? 0, 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
            <div class="px-4 py-5 sm:p-6">
                <!-- Filter Form -->
                <form method="GET" action="{{ route('setup.jkn-cbg-codes.base-tariff') }}" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Search') }}</label>
                            <input 
                                type="text" 
                                name="search" 
                                id="search"
                                value="{{ $search ?? '' }}" 
                                placeholder="{{ __('Code, name, or description...') }}" 
                                class="w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                        </div>
                        <div>
                            <label for="service_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Service Type') }}</label>
                            <select 
                                name="service_type" 
                                id="service_type"
                                class="w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                <option value="">{{ __('All Types') }}</option>
                                <option value="Rawat Inap" {{ $serviceType == 'Rawat Inap' ? 'selected' : '' }}>{{ __('Rawat Inap') }}</option>
                                <option value="Rawat Jalan" {{ $serviceType == 'Rawat Jalan' ? 'selected' : '' }}>{{ __('Rawat Jalan') }}</option>
                            </select>
                        </div>
                        <div>
                            <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Status') }}</label>
                            <select 
                                name="is_active" 
                                id="is_active"
                                class="w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                <option value="">{{ __('All Status') }}</option>
                                <option value="1" {{ $isActive === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="0" {{ $isActive === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button 
                                type="submit" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700"
                            >
                                {{ __('Filter') }}
                            </button>
                            @if($search || $serviceType || $isActive !== null)
                                <a 
                                    href="{{ route('setup.jkn-cbg-codes.base-tariff') }}" 
                                    class="ml-2 inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                                >
                                    {{ __('Clear') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                @if($cbgCodes->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        {{ __('Code') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        {{ __('Name') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        {{ __('Service Type') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        {{ __('Severity Level') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        {{ __('Base Tariff (INA-CBG)') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        {{ __('Status') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        {{ __('Grouping Version') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @foreach($cbgCodes as $cbgCode)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $cbgCode->code }}
                                        </td>
                                        <td class="px-6 py-2 text-sm text-gray-500 dark:text-gray-300">
                                            <div class="max-w-xs">
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $cbgCode->name }}</div>
                                                @if($cbgCode->description)
                                                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1 truncate">{{ Str::limit($cbgCode->description, 60) }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            @if($cbgCode->service_type)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $cbgCode->service_type == 'Rawat Inap' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                                    {{ $cbgCode->service_type }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            @if($cbgCode->severity_level)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    Level {{ $cbgCode->severity_level }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-right font-semibold text-gray-900 dark:text-white">
                                            Rp {{ number_format($cbgCode->tariff, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            @if($cbgCode->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                    {{ __('Active') }}
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                                    {{ __('Inactive') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $cbgCode->grouping_version ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $cbgCodes->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('No CBG codes found') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Try adjusting your search or filter criteria.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
