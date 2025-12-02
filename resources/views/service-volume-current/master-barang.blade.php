@extends('layouts.app')

@section('title', 'Service Volume (Current) - Master Barang')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="grid gap-6 md:grid-cols-5">
            <!-- Hidden inputs to preserve sort state when filtering -->
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="direction" value="{{ $direction }}">
            <input type="hidden" name="per_page" value="{{ $perPage }}">

            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select id="year" name="year" class="w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700">
                    @foreach($availableYears as $yearOption)
                        <option value="{{ $yearOption }}" {{ (int) $year === (int) $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="poli" class="block text-sm font-medium text-gray-700 mb-1">Poliklinik</label>
                <select id="poli" name="poli" class="w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700">
                    <option value="">Semua Poli</option>
                    @foreach($poliOptions as $option)
                        <option value="{{ $option->kd_poli }}" {{ $poli === $option->kd_poli ? 'selected' : '' }}>
                            {{ $option->kd_poli }} - {{ $option->nm_poli }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Nama Barang</label>
                <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Nama barang..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700">
            </div>
            <div class="flex items-end space-x-2">
                <div>
                    <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">Per Halaman</label>
                    <select id="per_page" name="per_page" class="w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700">
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        <option value="200" {{ $perPage == 200 ? 'selected' : '' }}>200</option>
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center justify-center w-full md:w-auto px-4 py-2 bg-biru-dongker-800 text-white rounded-md shadow hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    Terapkan Filter
                </button>
                <button type="submit"
                        formaction="{{ route('svc-current.master-barang.export') }}"
                        class="inline-flex items-center justify-center w-full md:w-auto px-4 py-2 bg-emerald-600 text-white rounded-md shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    Export Excel
                </button>
            </div>
        </form>
    </div>

    @if($errorMessage)
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ $errorMessage }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Rekap Penggunaan Obat/BHP</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Tahun {{ $year }}{{ $poli ? ' • Poli ' . $poli : '' }}
                        @if($barangData->total() > 0)
                            • Menampilkan {{ $barangData->firstItem() }}-{{ $barangData->lastItem() }} dari {{ number_format($barangData->total(), 0, ',', '.') }} data
                        @endif
                    </p>
                </div>
                <p class="text-sm text-gray-500">Sumber data: SIMRS</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700">Nama Barang</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700">Harga (Rp)</th>
                        @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'] as $bulan)
                            <th scope="col" class="px-3 py-3 text-center font-semibold text-gray-700">{{ $bulan }}</th>
                        @endforeach
                        <th scope="col" class="px-4 py-3 text-center font-semibold text-gray-700 bg-rose-50">Total Jumlah</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700 bg-rose-50 cursor-pointer hover:bg-rose-100 transition-colors"
                            onclick="window.location.href='{{ route('svc-current.master-barang', ['year' => $year, 'poli' => $poli, 'search' => $search, 'sort' => 'total_pendapatan', 'direction' => $sort === 'total_pendapatan' && $direction === 'desc' ? 'asc' : 'desc']) }}'">
                            <div class="flex items-center justify-end space-x-1">
                                <span>Total Pendapatan (Rp)</span>
                                @if($sort === 'total_pendapatan')
                                    @if($direction === 'asc')
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    @else
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    @endif
                                @else
                                    <svg class="w-4 h-4 text-gray-400 opacity-0 group-hover:opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                @endif
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($barangData as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $row['nama'] }}</div>
                                <div class="text-xs text-gray-500">Kode: {{ $row['kode'] }}</div>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($row['harga'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['jan'] == 0 ? '-' : number_format($row['jan'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['feb'] == 0 ? '-' : number_format($row['feb'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['mar'] == 0 ? '-' : number_format($row['mar'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['apr'] == 0 ? '-' : number_format($row['apr'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['may'] == 0 ? '-' : number_format($row['may'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['jun'] == 0 ? '-' : number_format($row['jun'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['jul'] == 0 ? '-' : number_format($row['jul'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['aug'] == 0 ? '-' : number_format($row['aug'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['sep'] == 0 ? '-' : number_format($row['sep'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['oct'] == 0 ? '-' : number_format($row['oct'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['nov'] == 0 ? '-' : number_format($row['nov'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['dec'] == 0 ? '-' : number_format($row['dec'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center font-semibold bg-rose-50">{{ number_format($row['total_jumlah'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold bg-rose-50">{{ number_format($row['total_pendapatan'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="px-4 py-6 text-center text-gray-500">Belum ada data obat/BHP untuk filter yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($barangData->count() > 0)
                    <tfoot class="bg-gray-50">
                        <tr class="font-semibold">
                            <td class="px-4 py-3 text-gray-900">Grand Total (Semua Data)</td>
                            <td class="px-4 py-3 text-right"></td>
                            <td class="px-3 py-3 text-center">{{ number_format($grandTotals['jan'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($grandTotals['feb'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($grandTotals['mar'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($grandTotals['apr'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($grandTotals['may'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($grandTotals['jun'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($grandTotals['jul'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($grandTotals['aug'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($grandTotals['sep'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($grandTotals['oct'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($grandTotals['nov'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($grandTotals['dec'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center bg-rose-50">{{ number_format($grandTotals['total_jumlah'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right bg-rose-50">{{ number_format($grandTotals['total_pendapatan'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if($barangData->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan {{ $barangData->firstItem() }} sampai {{ $barangData->lastItem() }} dari {{ number_format($barangData->total(), 0, ',', '.') }} hasil
                    </div>
                    <div>
                        {{ $barangData->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
