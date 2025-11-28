@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Compare Unit Cost Versions') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('compare-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is Version Comparison?') }}"
                    title="{{ __('What is Version Comparison?') }}"
                >
                    i
                </button>
            </div>
            <a href="{{ route('costing-process.unit-cost.results') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('View Results') }}
            </a>
        </div>

        <div id="compare-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Version Comparison</span> memungkinkan Anda membandingkan 2 versi perhitungan unit cost untuk melihat perubahan biaya per layanan. Berguna untuk analisis trend, validasi perubahan, atau audit perhitungan.
            </p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li>Pilih 2 versi berbeda untuk dibandingkan</li>
                <li>Sistem akan menampilkan selisih (variance) dalam rupiah dan persentase</li>
                <li>Layanan yang hanya ada di salah satu versi akan ditandai</li>
                <li>Gunakan filter untuk fokus pada periode atau cost center tertentu</li>
            </ul>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Version Selection & Filters -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('costing-process.unit-cost.compare') }}" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="version1" class="block text-sm font-medium text-gray-700">{{ __('Version 1 (Baseline)') }} <span class="text-red-500">*</span></label>
                            <select id="version1" name="version1" required class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                <option value="">{{ __('Select Version') }}</option>
                                @foreach($availableVersions as $version)
                                    <option value="{{ $version }}" {{ $version1 == $version ? 'selected' : '' }}>{{ $version }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="version2" class="block text-sm font-medium text-gray-700">{{ __('Version 2 (Compare To)') }} <span class="text-red-500">*</span></label>
                            <select id="version2" name="version2" required class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                <option value="">{{ __('Select Version') }}</option>
                                @foreach($availableVersions as $version)
                                    <option value="{{ $version }}" {{ $version2 == $version ? 'selected' : '' }}>{{ $version }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                        <div>
                            <label for="period_year" class="block text-sm font-medium text-gray-700">{{ __('Year') }}</label>
                            <select id="period_year" name="period_year" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                                <option value="">{{ __('All Years') }}</option>
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ $periodYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        
                        <div>
                            <label for="period_month" class="block text-sm font-medium text-gray-700">{{ __('Month') }}</label>
                            <select id="period_month" name="period_month" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                                <option value="">{{ __('All Months') }}</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $periodMonth == $m ? 'selected' : '' }}>
                                        {{ str_pad($m, 2, '0', STR_PAD_LEFT) }} - {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        
                        <div>
                            <label for="cost_center_id" class="block text-sm font-medium text-gray-700">{{ __('Cost Center') }}</label>
                            <select id="cost_center_id" name="cost_center_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                                <option value="">{{ __('All Cost Centers') }}</option>
                                @foreach($costCenters as $cc)
                                    <option value="{{ $cc->id }}" {{ $costCenterId == $cc->id ? 'selected' : '' }}>
                                        {{ $cc->code }} - {{ $cc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">{{ __('Search') }}</label>
                            <input type="text" id="search" name="search" value="{{ $search }}" placeholder="{{ __('Service code/name...') }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                                {{ __('Compare') }}
                            </button>
                        </div>
                    </div>
                    
                    @if($version1 || $version2 || $periodYear || $periodMonth || $costCenterId || $search)
                        <div>
                            <a href="{{ route('costing-process.unit-cost.compare') }}" class="text-sm text-biru-dongker-800 hover:text-biru-dongker-900">
                                {{ __('Clear Filters') }}
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        @if($version1 && $version2 && $version1 !== $version2)
            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-5 mb-6">
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
                                    <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Version 1 Services') }}</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($summaryStats['version1_count'], 0, ',', '.') }}</dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Version 2 Services') }}</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($summaryStats['version2_count'], 0, ',', '.') }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-biru-dongker-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Common Services') }}</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($summaryStats['common_services'], 0, ',', '.') }}</dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Only in V1') }}</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($summaryStats['only_in_version1'], 0, ',', '.') }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Avg Variance %') }}</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($summaryStats['avg_variance'], 2) }}%</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparison Table -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Comparison Details') }}</h3>
                        <div class="flex items-center gap-4 text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-blue-100 border border-blue-300"></div>
                                <span class="text-gray-600">{{ $version1 }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-green-100 border border-green-300"></div>
                                <span class="text-gray-600">{{ $version2 }}</span>
                            </div>
                        </div>
                    </div>
                    
                    @if(count($comparisonData) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Cost Center') }}</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" colspan="4">
                                            <span class="text-blue-600">{{ $version1 }}</span>
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" colspan="4">
                                            <span class="text-green-600">{{ $version2 }}</span>
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" colspan="2">{{ __('Variance') }}</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th class="px-6 py-2 text-xs font-medium text-gray-500">{{ __('Period') }}</th>
                                        <th class="px-6 py-2 text-xs font-medium text-gray-500 text-right">{{ __('Material') }}</th>
                                        <th class="px-6 py-2 text-xs font-medium text-gray-500 text-right">{{ __('Labor') }}</th>
                                        <th class="px-6 py-2 text-xs font-medium text-gray-500 text-right">{{ __('Total') }}</th>
                                        <th class="px-6 py-2 text-xs font-medium text-gray-500">{{ __('Period') }}</th>
                                        <th class="px-6 py-2 text-xs font-medium text-gray-500 text-right">{{ __('Material') }}</th>
                                        <th class="px-6 py-2 text-xs font-medium text-gray-500 text-right">{{ __('Labor') }}</th>
                                        <th class="px-6 py-2 text-xs font-medium text-gray-500 text-right">{{ __('Total') }}</th>
                                        <th class="px-6 py-2 text-xs font-medium text-gray-500 text-right">{{ __('Amount') }}</th>
                                        <th class="px-6 py-2 text-xs font-medium text-gray-500 text-right">{{ __('%') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($comparisonData as $item)
                                        <tr class="{{ !$item['version1'] ? 'bg-yellow-50' : (!$item['version2'] ? 'bg-red-50' : '') }}">
                                            <td class="px-6 py-4 text-sm">
                                                <div class="font-medium text-gray-900">{{ $item['service_code'] }}</div>
                                                <div class="text-gray-500 text-xs">{{ Str::limit($item['service_description'], 40) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['cost_center'] }}</td>
                                            
                                            <!-- Version 1 -->
                                            @if($item['version1'])
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ $item['version1']['period'] }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right bg-blue-50">{{ number_format($item['version1']['direct_material'], 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right bg-blue-50">{{ number_format($item['version1']['direct_labor'], 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right bg-blue-50">{{ number_format($item['version1']['total'], 0, ',', '.') }}</td>
                                            @else
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 text-center" colspan="4">-</td>
                                            @endif
                                            
                                            <!-- Version 2 -->
                                            @if($item['version2'])
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ $item['version2']['period'] }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right bg-green-50">{{ number_format($item['version2']['direct_material'], 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right bg-green-50">{{ number_format($item['version2']['direct_labor'], 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right bg-green-50">{{ number_format($item['version2']['total'], 0, ',', '.') }}</td>
                                            @else
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 text-center" colspan="4">-</td>
                                            @endif
                                            
                                            <!-- Variance -->
                                            @if(isset($item['variance']))
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right {{ $item['variance']['amount'] >= 0 ? 'text-red-600' : 'text-green-600' }}">
                                                    {{ $item['variance']['amount'] >= 0 ? '+' : '' }}{{ number_format($item['variance']['amount'], 0, ',', '.') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right {{ $item['variance']['percent'] >= 0 ? 'text-red-600' : 'text-green-600' }}">
                                                    {{ $item['variance']['percent'] >= 0 ? '+' : '' }}{{ number_format($item['variance']['percent'], 2) }}%
                                                </td>
                                            @else
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 text-center" colspan="2">-</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No comparison data found') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('No matching services found for the selected versions and filters.') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Select Versions to Compare') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('Please select two different versions above to start comparison.') }}
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
