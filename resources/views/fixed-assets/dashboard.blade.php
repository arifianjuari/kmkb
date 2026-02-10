@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Dashboard Aset Tetap') }}</h2>
        <div class="flex items-center space-x-2">
            <a href="{{ route('fixed-assets.index') }}" class="btn-secondary">{{ __('Daftar Aset') }}</a>
            <a href="{{ route('fixed-assets.depreciation') }}" class="btn-primary">{{ __('Kalkulator Depresiasi') }}</a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Aset</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalAssets) }}</p>
                    <p class="text-sm text-gray-500">{{ $activeAssets }} aktif</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Nilai Perolehan</p>
                    <p class="text-xl font-semibold text-gray-900">Rp {{ number_format($totalAcquisitionCost, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Nilai Buku</p>
                    <p class="text-xl font-semibold text-gray-900">Rp {{ number_format($totalBookValue, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Depresiasi/Bulan</p>
                    <p class="text-xl font-semibold text-gray-900">Rp {{ number_format($totalMonthlyDepreciation, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sync Alert -->
    @if($unsyncedCount > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <p class="ml-3 text-sm text-yellow-700">
                <strong>{{ $unsyncedCount }}</strong> record depresiasi belum di-sync ke GL Expenses.
                <a href="{{ route('fixed-assets.depreciation') }}" class="underline font-medium">Sync sekarang →</a>
            </p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Distribution by Jenis BMN -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Distribusi per Jenis BMN</h3>
            @if($jenisBmnDistribution->count() > 0)
            <div class="space-y-3">
                @foreach($jenisBmnDistribution as $item)
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $item->jenis_bmn }}</span>
                            <span class="text-sm text-gray-500">{{ $item->count }} aset</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($item->total_value / $totalAcquisitionCost) * 100 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Rp {{ number_format($item->total_value, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-sm">Belum ada data jenis BMN.</p>
            @endif
        </div>

        <!-- Top 10 Assets -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top 10 Aset (Nilai Terbesar)</h3>
            @if($topAssets->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500">Nama</th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($topAssets as $asset)
                        <tr>
                            <td class="px-2 py-2 text-sm text-gray-900">
                                <a href="{{ route('fixed-assets.show', $asset) }}" class="hover:text-blue-600">{{ Str::limit($asset->name, 35) }}</a>
                            </td>
                            <td class="px-2 py-2 text-sm text-gray-900 text-right">Rp {{ number_format($asset->acquisition_cost, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-500 text-sm">Belum ada data aset.</p>
            @endif
        </div>
    </div>

    <!-- Recent Depreciations -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Depresiasi Terbaru</h3>
            <a href="{{ route('fixed-assets.depreciation-report') }}" class="text-sm text-blue-600 hover:underline">Lihat Laporan →</a>
        </div>
        @if($recentDepreciations->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Periode</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Aset</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Depresiasi</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Status Sync</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentDepreciations as $dep)
                    <tr>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $dep->period_display }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ Str::limit($dep->fixedAsset->name ?? '-', 30) }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900 text-right">Rp {{ number_format($dep->depreciation_amount, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-sm text-center">
                            @if($dep->is_synced_to_gl)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Synced</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-sm">Belum ada data depresiasi.</p>
        @endif
    </div>
</div>
@endsection
