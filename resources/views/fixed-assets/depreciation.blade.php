@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Kalkulator Depresiasi') }}</h2>
        <div class="flex space-x-2">
            <a href="{{ route('fixed-assets.export-depreciation') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">{{ __('Export Laporan') }}</a>
            <a href="{{ route('fixed-assets.index') }}" class="btn-secondary">{{ __('Kembali') }}</a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-sm text-gray-500">Total Aset Aktif</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($summary['total_assets']) }}</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-sm text-gray-500">Total Harga Perolehan</div>
            <div class="text-2xl font-bold text-blue-600">Rp {{ number_format($summary['total_acquisition_cost'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-sm text-gray-500">Total Depresiasi/Bulan</div>
            <div class="text-2xl font-bold text-orange-600">Rp {{ number_format($summary['total_monthly_depreciation'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-sm text-gray-500">Total Nilai Buku</div>
            <div class="text-2xl font-bold text-green-600">Rp {{ number_format($summary['total_book_value'], 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Calculate Form -->
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Hitung Depresiasi Periode</h3>
            <form action="{{ route('fixed-assets.calculate-depreciation') }}" method="POST" class="flex items-end gap-4">
                @csrf
                <div>
                    <label for="period_month" class="block text-sm font-medium text-gray-700">Bulan</label>
                    <select name="period_month" id="period_month" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="period_year" class="block text-sm font-medium text-gray-700">Tahun</label>
                    <select name="period_year" id="period_year" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                        @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="btn-primary">Hitung Depresiasi</button>
            </form>
        </div>
    </div>

    <!-- Asset List -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Aset Aktif</h3>
            @if($assets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Aset</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Perolehan</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Umur</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Depresiasi/Bln</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Akumulasi</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nilai Buku</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($assets as $index => $asset)
                                <tr class="{{ $asset->is_fully_depreciated ? 'bg-red-50' : '' }}">
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $asset->asset_code }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $asset->name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $asset->assetCategory?->name ?? '-' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right">Rp {{ number_format($asset->acquisition_cost, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500 text-center">{{ $asset->useful_life_years }} thn</td>
                                    <td class="px-4 py-2 text-sm text-blue-600 text-right">Rp {{ number_format($asset->monthly_depreciation, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-orange-600 text-right">Rp {{ number_format($asset->getAccumulatedDepreciation(), 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm font-medium text-right {{ $asset->is_fully_depreciated ? 'text-red-600' : 'text-green-600' }}">Rp {{ number_format($asset->current_book_value, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>Belum ada aset aktif.</p>
                    <a href="{{ route('fixed-assets.create') }}" class="mt-4 inline-block btn-primary">+ Tambah Aset</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
