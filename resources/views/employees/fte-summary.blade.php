@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:justify-between md:items-center gap-4 dark:border-gray-700">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Rekap FTE per Cost Center</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ringkasan FTE pegawai aktif per unit kerja</p>
            </div>
            <a href="{{ route('employees.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Period Filter & Generate -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <form method="GET" action="{{ route('employees.fte-summary') }}" class="flex gap-4 items-end flex-1">
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bulan</label>
                        <select name="month" id="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun</label>
                        <select name="year" id="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @for($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Tampilkan</button>
                </form>

                <form method="POST" action="{{ route('employees.generate-fte') }}" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button type="submit" class="btn-success" onclick="return confirm('Generate FTE ke Driver Statistics untuk periode {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}?')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Generate ke Driver Statistics
                    </button>
                </form>
            </div>
        </div>

        <!-- FTE Summary Table -->
        <div class="p-6">
            @if($fteData->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Kode</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Cost Center</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Jumlah Pegawai</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Total FTE</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @php $totalEmployees = 0; $totalFte = 0; @endphp
                            @foreach($fteData as $item)
                                @php 
                                    $totalEmployees += $item->employee_count; 
                                    $totalFte += $item->total_fte; 
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">{{ $item->code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $item->employee_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium text-gray-900 dark:text-gray-100">{{ number_format($item->total_fte, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <td colspan="2" class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-white">TOTAL</td>
                                <td class="px-6 py-3 text-sm text-center font-bold text-gray-900 dark:text-white">{{ $totalEmployees }}</td>
                                <td class="px-6 py-3 text-sm text-center font-bold text-gray-900 dark:text-white">{{ number_format($totalFte, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-6 p-4 bg-blue-50 rounded-lg dark:bg-blue-900">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Cara Menggunakan</h4>
                            <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                Klik tombol <strong>"Generate ke Driver Statistics"</strong> untuk otomatis mengisi nilai FTE per cost center ke menu Driver Statistics. 
                                Data akan digunakan untuk alokasi biaya SDM menggunakan driver "Jumlah Karyawan (FTE)".
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada data FTE</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada pegawai aktif dengan penempatan untuk periode ini.</p>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
