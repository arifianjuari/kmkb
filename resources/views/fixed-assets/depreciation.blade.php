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

    <!-- Notifications -->
    @if(session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4 border border-green-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-6 rounded-md bg-yellow-50 p-4 border border-yellow-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-yellow-800">{{ session('warning') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-md bg-red-50 p-4 border border-red-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

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

    <!-- Sync to GL Form -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-lg font-medium text-blue-800">Sync ke GL Expenses</h3>
                    <p class="text-sm text-blue-700 mt-1">Kirim hasil depresiasi ke tabel GL Expenses agar masuk dalam perhitungan Unit Cost.</p>
                    <form action="{{ route('fixed-assets.sync-depreciation') }}" method="POST" class="mt-4 flex flex-wrap items-end gap-4">
                        @csrf
                        <div>
                            <label for="sync_month" class="block text-sm font-medium text-blue-800">Bulan</label>
                            <select name="period_month" id="sync_month" required class="mt-1 block w-full border-blue-300 rounded-md shadow-sm sm:text-sm bg-white">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="sync_year" class="block text-sm font-medium text-blue-800">Tahun</label>
                            <select name="period_year" id="sync_year" required class="mt-1 block w-full border-blue-300 rounded-md shadow-sm sm:text-sm bg-white">
                                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label for="expense_category_id" class="block text-sm font-medium text-blue-800">Kode Akun (COA) <span class="text-xs font-normal">(opsional)</span></label>
                            <select name="expense_category_id" id="expense_category_id" class="mt-1 block w-full border-blue-300 rounded-md shadow-sm sm:text-sm bg-white">
                                <option value="">-- Buat otomatis (6900 - Biaya Penyusutan) --</option>
                                @foreach($expenseCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->account_code }} - {{ $cat->account_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Sync ke GL
                        </button>
                        <a href="{{ route('fixed-assets.depreciation-report') }}" class="btn-secondary">Lihat Laporan</a>
                    </form>
                </div>
            </div>
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
