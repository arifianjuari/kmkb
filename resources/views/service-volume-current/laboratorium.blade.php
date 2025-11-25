@extends('layouts.app')

@section('title', 'Service Volume (Current) - Laboratorium')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 space-y-6">
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="grid gap-6 md:grid-cols-4">
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select id="year" name="year" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach($availableYears as $yearOption)
                        <option value="{{ $yearOption }}" {{ (int) $year === (int) $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Pasien</label>
                <select id="status" name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="all" {{ $status === null ? 'selected' : '' }}>Semua Status</option>
                    <option value="Ralan" {{ $status === 'Ralan' ? 'selected' : '' }}>Rawat Jalan</option>
                    <option value="Ranap" {{ $status === 'Ranap' ? 'selected' : '' }}>Rawat Inap</option>
                </select>
            </div>
            <div>
                <label for="poli" class="block text-sm font-medium text-gray-700 mb-1">Poliklinik</label>
                <select id="poli" name="poli" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Poli</option>
                    @foreach($poliOptions as $option)
                        <option value="{{ $option->kd_poli }}" {{ $poli === $option->kd_poli ? 'selected' : '' }}>
                            {{ $option->kd_poli }} - {{ $option->nm_poli }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Nama Pemeriksaan</label>
                <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Nama pemeriksaan..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex items-end space-x-2 md:col-span-4">
                <button type="submit" class="inline-flex items-center justify-center w-full md:w-auto px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Terapkan Filter
                </button>
                <button type="submit"
                        formaction="{{ route('svc-current.laboratorium.export') }}"
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
                    <h2 class="text-lg font-semibold text-gray-900">Rekap Pemeriksaan Laboratorium</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Tahun {{ $year }}
                        @if($status)
                            • Status {{ $status === 'Ralan' ? 'Rawat Jalan' : 'Rawat Inap' }}
                        @endif
                        @if($poli)
                            • Poli {{ $poli }}
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
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700">Nama Pemeriksaan</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700">Harga (Rp)</th>
                        @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'] as $bulan)
                            <th scope="col" class="px-3 py-3 text-center font-semibold text-gray-700">{{ $bulan }}</th>
                        @endforeach
                        <th scope="col" class="px-4 py-3 text-center font-semibold text-gray-700 bg-rose-50">Total Jumlah Pemeriksaan</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700 bg-rose-50">Total Pendapatan (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($laboratoriumData as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $row['nama'] }}</div>
                                <div class="text-xs text-gray-500">Kode: {{ $row['kode'] }}</div>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($row['harga'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['jan'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['feb'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['mar'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['apr'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['may'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['jun'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['jul'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['aug'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['sep'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['oct'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['nov'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $row['dec'] }}</td>
                            <td class="px-4 py-3 text-center font-semibold bg-rose-50">{{ $row['total_tindakan'] }}</td>
                            <td class="px-4 py-3 text-right font-semibold bg-rose-50">{{ number_format($row['total_pendapatan'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="px-4 py-6 text-center text-gray-500">Belum ada data laboratorium untuk filter yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($laboratoriumData) > 0)
                    <tfoot class="bg-gray-50">
                        <tr class="font-semibold">
                            <td class="px-4 py-3 text-gray-900">Grand Total</td>
                            <td class="px-4 py-3"></td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['jan'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['feb'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['mar'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['apr'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['may'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['jun'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['jul'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['aug'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['sep'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['oct'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['nov'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['dec'] }}</td>
                            <td class="px-4 py-3 text-center bg-rose-50">{{ $grandTotals['total_tindakan'] }}</td>
                            <td class="px-4 py-3 text-right bg-rose-50">{{ number_format($grandTotals['total_pendapatan'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection

