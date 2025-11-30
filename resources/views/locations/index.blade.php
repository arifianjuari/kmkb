@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Location') }}</h2>
            <div class="flex items-end gap-3 flex-wrap">
                <form method="GET" action="{{ route('locations.index') }}" class="flex items-end gap-3 flex-wrap">
                    <div class="w-32">
                        <label for="period_year" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Year') }}</label>
                        <select id="period_year" name="period_year" class="block w-full py-2 pl-3 pr-10 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 bg-white">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $periodYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="w-40">
                        <label for="period_month" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Month') }}</label>
                        <select id="period_month" name="period_month" class="block w-full py-2 pl-3 pr-10 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 bg-white">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $periodMonth == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex-shrink-0">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700 whitespace-nowrap">
                            {{ __('Filter') }}
                        </button>
                    </div>
                </form>
                @if(count($locations) > 0)
                <div class="flex-shrink-0">
                    <a href="{{ route('locations.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 whitespace-nowrap">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ __('Export Excel') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="mb-4 text-sm text-gray-600">
                    {{ __('Showing data for period') }}: <strong>{{ date('F', mktime(0, 0, 0, $periodMonth, 1)) }} {{ $periodYear }}</strong>
                </div>
                @if(count($locations) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-300">{{ __('No') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-300">{{ __('Nama Gedung') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-300">{{ __('Lantai') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-300">{{ __('Bagian') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-300">{{ __('Luas (m2)') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-300">{{ __('Kelas') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-300">{{ __('Jumlah Tempat Tidur per Ruangan') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-300">{{ __('Jumlah Ruang Perawatan') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($locations as $location)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border border-gray-300 text-center">{{ $location['no'] }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 border border-gray-300">{{ $location['building_name'] ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border border-gray-300 text-center">{{ $location['floor'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 border border-gray-300">{{ $location['bagian'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border border-gray-300 text-right">{{ $location['luas_m2'] !== null ? number_format($location['luas_m2'], 0, ',', '.') : '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border border-gray-300">{{ $location['kelas'] ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border border-gray-300 text-right">{{ $location['jumlah_tempat_tidur'] !== null ? number_format($location['jumlah_tempat_tidur'], 0, ',', '.') : '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border border-gray-300 text-right">{{ $location['jumlah_ruang_perawatan'] !== null ? number_format($location['jumlah_ruang_perawatan'], 0, ',', '.') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No location data found') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('No cost centers with building information or driver statistics found for the selected period.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

