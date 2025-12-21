@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Calculate Unit Cost') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('unit-cost-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is Unit Cost Calculation?') }}"
                    title="{{ __('What is Unit Cost Calculation?') }}"
                >
                    i
                </button>
            </div>
            <a href="{{ route('costing-process.unit-cost.results') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('View Calculation Results') }}
            </a>
        </div>

        <div id="unit-cost-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Unit Cost Calculation</span> menjalankan perhitungan biaya per layanan dengan menggabungkan GL (direct cost), hasil alokasi overhead, serta volume layanan untuk periode tertentu. Simpan setiap kalkulasi sebagai <em>version</em> agar bisa dibandingkan dengan versi sebelumnya atau digunakan untuk simulasi tarif.
            </p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li>Pastikan pre-allocation checks sudah hijau (GL, Driver, Volume, Mapping)</li>
                <li>Gunakan format versi seperti <code>UC-2025-01</code> agar mudah ditelusuri</li>
                <li>Hasil kalkulasi tersedia di menu <strong>Unit Cost Results</strong> & dapat dipakai oleh modul Tariff</li>
            </ul>
        </div>



        @if(session('unit_cost_errors'))
            <div class="mb-6 rounded-md bg-yellow-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 w-full">
                        <p class="text-sm font-medium text-yellow-800">{{ __('Detail peringatan perhitungan:') }}</p>
                        <ul class="mt-2 list-disc list-inside text-sm text-yellow-700 space-y-1">
                            @foreach(session('unit_cost_errors', []) as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if(session('unit_cost_warnings'))
            <div class="mb-6 rounded-md bg-blue-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10A8 8 0 11.001 10 8 8 0 0118 10zm-8-5a1 1 0 00-.993.883L9 6v3a1 1 0 001.993.117L11 9V6a1 1 0 00-1-1zm0 8a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 w-full">
                        <p class="text-sm font-medium text-blue-800">{{ __('Catatan tambahan:') }}</p>
                        <ul class="mt-2 list-disc list-inside text-sm text-blue-700 space-y-1">
                            @foreach(session('unit_cost_warnings', []) as $warning)
                                <li>{{ $warning }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Run Unit Cost Calculation') }}</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <form action="{{ route('costing-process.unit-cost.calculate.run') }}" method="POST" onsubmit="return confirm('{{ __('Jalankan perhitungan unit cost untuk periode ini? Versi yang sama akan ditimpa.') }}')">
                        @csrf
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700">{{ __('Year') }} <span class="text-red-500">*</span></label>
                                <select id="year" name="year" required class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" {{ old('year', $year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                                @error('year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="month" class="block text-sm font-medium text-gray-700">{{ __('Month') }} <span class="text-red-500">*</span></label>
                                <select id="month" name="month" required class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ old('month', $month) == $m ? 'selected' : '' }}>
                                            {{ str_pad($m, 2, '0', STR_PAD_LEFT) }} - {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
                                        </option>
                                    @endfor
                                </select>
                                @error('month')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="version_label" class="block text-sm font-medium text-gray-700">{{ __('Version Label') }} <span class="text-red-500">*</span></label>
                                <input type="text" id="version_label" name="version_label" required maxlength="100" value="{{ old('version_label', $suggestedVersion) }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700" placeholder="UC-{{ date('Y') }}{{ date('m') }}" />
                                <p class="mt-1 text-xs text-gray-500">{{ __('Gunakan format konsisten, misal: UC-2025-01 atau UC-JAN25-V1.') }}</p>
                                @error('version_label')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                                {{ __('Run Calculation') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Readiness Check') }}</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-4">
                        @foreach($readiness as $item)
                            <div class="flex items-start justify-between p-3 border rounded-lg {{ $item['status'] ? 'border-green-200 bg-green-50' : 'border-yellow-200 bg-yellow-50' }}">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $item['label'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $item['description'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900">{{ number_format($item['count'], 0, ',', '.') }}</p>
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full {{ $item['status'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $item['status'] ? __('Ready') : __('Need Attention') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                        <p class="text-xs text-gray-500">{{ __('Pastikan seluruh indikator siap sebelum menjalankan perhitungan. Data yang belum lengkap dapat menyebabkan layanan dilewati atau perhitungan tidak akurat.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-4 mt-6">
            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-xs text-gray-500">{{ __('Revenue Cost Centers') }}</p>
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($revenueCentersCount, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500">{{ __('Cost center yang dihitung unit cost-nya') }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-xs text-gray-500">{{ __('Service Catalog (mapped)') }}</p>
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($serviceCatalogCount, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500">{{ __('Layanan dengan cost center & expense category') }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-xs text-gray-500">{{ __('Service Volume Records') }}</p>
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($serviceVolumeCount, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500">{{ __('Entri volume periode ini') }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-xs text-gray-500">{{ __('Allocation Entries') }}</p>
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($allocationResultCount, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500">{{ __('Total baris allocation results periode ini') }}</p>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-6">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Latest Unit Cost Versions') }}</h3>
                <span class="text-xs text-gray-500">{{ __('Menampilkan 10 versi terakhir') }}</span>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Version') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Period') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Services Processed') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Last Run At') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($existingVersions as $version)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{{ $version->version_label }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{{ str_pad($version->period_month, 2, '0', STR_PAD_LEFT) }}/{{ $version->period_year }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($version->services, 0, ',', '.') }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
                                        {{ $version->last_run_at ? \Carbon\Carbon::parse($version->last_run_at)->locale('id')->isoFormat('DD MMM YYYY HH:mm') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-2 text-sm text-gray-500 text-center">{{ __('Belum ada perhitungan unit cost yang tersimpan.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection







