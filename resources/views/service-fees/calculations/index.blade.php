@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:justify-between md:items-center gap-4 dark:border-gray-700">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Hasil Perhitungan Jasa</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Jasa tenaga kesehatan per layanan</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('service-fees.calculations.form') }}" class="btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Hitung Ulang
                </a>
                <a href="{{ route('service-fees.calculations.export', ['year' => $year, 'month' => $month]) }}" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun</label>
                    <input type="number" id="year" name="year" value="{{ $year }}" min="2020" max="2099"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                </div>
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bulan</label>
                    <select id="month" name="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                        <option value="">Semua Bulan</option>
                        @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $num => $name)
                            <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cari Layanan</label>
                    <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Nama atau kode..."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary">Filter</button>
                    <a href="{{ route('service-fees.calculations.index') }}" class="btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <!-- Summary -->
        @if($summary)
        <div class="p-6 border-b border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Layanan</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($summary->count) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Index Points</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($summary->total_points, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Jasa</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($summary->total_fee, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Table -->
        <div class="p-6">
            @if($calculations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Layanan</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Periode</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Index Points</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Nilai/Point</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Jasa</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($calculations as $calc)
                                <tr>
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $calc->costReference->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $calc->costReference->code ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-center text-sm text-gray-500">{{ $calc->period_label }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100">{{ number_format($calc->total_index_points, 2) }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100">Rp {{ number_format($calc->point_value, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-semibold text-green-600">{{ $calc->formatted_fee }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-center">
                                        <a href="{{ route('service-fees.calculations.show', $calc) }}" class="text-biru-dongker-600 hover:text-biru-dongker-900 text-sm">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $calculations->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Belum ada hasil perhitungan</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jalankan perhitungan jasa untuk melihat hasil.</p>
                    <div class="mt-6">
                        <a href="{{ route('service-fees.calculations.form') }}" class="btn-primary">Hitung Jasa</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
