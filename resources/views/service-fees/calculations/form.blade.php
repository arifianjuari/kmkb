@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:justify-between md:items-center gap-4 dark:border-gray-700">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Hitung Jasa Tenaga Kesehatan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Jalankan perhitungan jasa berdasarkan pendapatan dan indeks</p>
            </div>
            <a href="{{ route('service-fees.calculations.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Lihat Hasil
            </a>
        </div>

        <!-- Prerequisites Check -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Prasyarat Perhitungan:</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="flex items-center gap-2">
                    @if($hasRevenue)
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    @endif
                    <span class="text-sm {{ $hasRevenue ? 'text-green-700' : 'text-red-700' }}">Data Pendapatan</span>
                </div>
                <div class="flex items-center gap-2">
                    @if($configs->count() > 0)
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    @endif
                    <span class="text-sm {{ $configs->count() > 0 ? 'text-green-700' : 'text-red-700' }}">Konfigurasi Jasa</span>
                </div>
                <div class="flex items-center gap-2">
                    @if($hasIndexes)
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    @endif
                    <span class="text-sm {{ $hasIndexes ? 'text-green-700' : 'text-red-700' }}">Indeks Jasa</span>
                </div>
                <div class="flex items-center gap-2">
                    @if($hasAssignments)
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    @endif
                    <span class="text-sm {{ $hasAssignments ? 'text-green-700' : 'text-red-700' }}">Penugasan Jasa</span>
                </div>
            </div>
        </div>

        <form action="{{ route('service-fees.calculations.run') }}" method="POST" class="p-6">
            @csrf
            
            @if(!$hasRevenue || $configs->count() == 0 || !$hasIndexes || !$hasAssignments)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">Lengkapi semua prasyarat sebelum menjalankan perhitungan.</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="service_fee_config_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfigurasi Jasa <span class="text-red-500">*</span></label>
                    <select id="service_fee_config_id" name="service_fee_config_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                        <option value="">Pilih Konfigurasi</option>
                        @foreach($configs as $config)
                            <option value="{{ $config->id }}">{{ $config->name }} ({{ $config->period_year }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="period_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun <span class="text-red-500">*</span></label>
                    <input type="number" id="period_year" name="period_year" value="{{ date('Y') }}" required min="2020" max="2099"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                </div>

                <div>
                    <label for="period_month" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bulan <span class="text-red-500">*</span></label>
                    <select id="period_month" name="period_month" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                        @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $num => $name)
                            <option value="{{ $num }}" {{ $num == date('n') ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('service-fees.calculations.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary" {{ (!$hasRevenue || $configs->count() == 0 || !$hasIndexes || !$hasAssignments) ? 'disabled' : '' }}>
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Jalankan Perhitungan
                </button>
            </div>
        </form>
    </div>
</section>
@endsection
