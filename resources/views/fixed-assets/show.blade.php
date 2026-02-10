@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Detail Aset') }}: {{ $fixedAsset->name }}</h2>
        <div class="flex space-x-2">
            <a href="{{ route('fixed-assets.edit', $fixedAsset) }}" class="btn-primary">{{ __('Edit') }}</a>
            <a href="{{ route('fixed-assets.index') }}" class="btn-secondary">{{ __('Kembali') }}</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-2 gap-4">
                    <div><dt class="text-sm font-medium text-gray-500">Kode Aset</dt><dd class="mt-1 text-sm text-gray-900">{{ $fixedAsset->asset_code }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">Nama</dt><dd class="mt-1 text-sm text-gray-900">{{ $fixedAsset->name }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">Kategori</dt><dd class="mt-1 text-sm text-gray-900">{{ $fixedAsset->assetCategory?->name ?? '-' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">Cost Center</dt><dd class="mt-1 text-sm text-gray-900">{{ $fixedAsset->costCenter?->name ?? '-' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">Merk / Model</dt><dd class="mt-1 text-sm text-gray-900">{{ $fixedAsset->brand ?? '-' }} {{ $fixedAsset->model }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">Nomor Seri</dt><dd class="mt-1 text-sm text-gray-900">{{ $fixedAsset->serial_number ?? '-' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">Produsen</dt><dd class="mt-1 text-sm text-gray-900">{{ $fixedAsset->manufacturer ?? '-' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">Lokasi</dt><dd class="mt-1 text-sm text-gray-900">{{ $fixedAsset->location ?? '-' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">Kondisi</dt><dd class="mt-1"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $fixedAsset->condition == 'good' ? 'bg-green-100 text-green-800' : ($fixedAsset->condition == 'fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ $fixedAsset->condition_display }}</span></dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">Status</dt><dd class="mt-1"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $fixedAsset->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $fixedAsset->status_display }}</span></dd></div>
                    <div class="col-span-2"><dt class="text-sm font-medium text-gray-500">Deskripsi</dt><dd class="mt-1 text-sm text-gray-900">{{ $fixedAsset->description ?? '-' }}</dd></div>
                </dl>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Keuangan</h3>
                <dl class="space-y-3">
                    <div><dt class="text-sm text-gray-500">Tanggal Perolehan</dt><dd class="text-sm font-medium text-gray-900">{{ $fixedAsset->acquisition_date?->format('d M Y') }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Harga Perolehan</dt><dd class="text-lg font-semibold text-gray-900">Rp {{ number_format($fixedAsset->acquisition_cost, 0, ',', '.') }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Umur Ekonomis</dt><dd class="text-sm font-medium text-gray-900">{{ $fixedAsset->useful_life_years }} tahun</dd></div>
                    <div><dt class="text-sm text-gray-500">Nilai Sisa</dt><dd class="text-sm font-medium text-gray-900">Rp {{ number_format($fixedAsset->salvage_value, 0, ',', '.') }}</dd></div>
                    <hr>
                    <div><dt class="text-sm text-gray-500">Depresiasi / Bulan</dt><dd class="text-sm font-medium text-blue-600">Rp {{ number_format($fixedAsset->monthly_depreciation, 0, ',', '.') }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Akumulasi Depresiasi</dt><dd class="text-sm font-medium text-orange-600">Rp {{ number_format($fixedAsset->getAccumulatedDepreciation(), 0, ',', '.') }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Nilai Buku</dt><dd class="text-lg font-bold {{ $fixedAsset->is_fully_depreciated ? 'text-red-600' : 'text-green-600' }}">Rp {{ number_format($fixedAsset->current_book_value, 0, ',', '.') }}</dd></div>
                    @if($fixedAsset->is_fully_depreciated)
                        <div class="mt-2 p-2 bg-red-50 rounded text-xs text-red-700">⚠️ Aset sudah fully depreciated</div>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <!-- Depreciation Calculation Info -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-blue-800 mb-3">📊 Kalkulasi Depresiasi (Otomatis)</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <dt class="text-blue-600">Bulan Berlalu</dt>
                    <dd class="font-semibold text-blue-900">{{ number_format($fixedAsset->months_elapsed) }} bulan</dd>
                </div>
                <div>
                    <dt class="text-blue-600">Maks Bulan Depresiasi</dt>
                    <dd class="font-semibold text-blue-900">{{ number_format($fixedAsset->max_depreciation_months) }} bulan</dd>
                </div>
                <div>
                    <dt class="text-blue-600">Progress Depresiasi</dt>
                    <dd class="font-semibold text-blue-900">
                        {{ $fixedAsset->max_depreciation_months > 0 ? round(($fixedAsset->months_elapsed / $fixedAsset->max_depreciation_months) * 100, 1) : 0 }}%
                    </dd>
                </div>
                <div>
                    <dt class="text-blue-600">Sisa Bulan</dt>
                    <dd class="font-semibold text-blue-900">{{ max(0, $fixedAsset->max_depreciation_months - $fixedAsset->months_elapsed) }} bulan</dd>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-blue-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, $fixedAsset->max_depreciation_months > 0 ? ($fixedAsset->months_elapsed / $fixedAsset->max_depreciation_months) * 100 : 0) }}%"></div>
                </div>
            </div>
            <p class="mt-3 text-xs text-blue-600">
                <strong>Metode:</strong> Garis Lurus (Straight-Line) | 
                <strong>Rumus:</strong> (Harga Perolehan - Nilai Sisa) ÷ (Umur Ekonomis × 12) × Bulan Berlalu
            </p>
        </div>
    </div>
</div>
@endsection

