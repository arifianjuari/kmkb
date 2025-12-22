@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:justify-between md:items-center gap-4 dark:border-gray-700">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Konfigurasi Jasa</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pengaturan rasio pembagian jasa pelayanan dan distribusi internal</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('service-fees.configs.create') }}" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Konfigurasi
                </a>
            </div>
        </div>

        <!-- Year Filter -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <form method="GET" class="flex items-center gap-4">
                <label for="year" class="text-sm font-medium text-gray-700 dark:text-gray-300">Tahun:</label>
                <select id="year" name="year" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                    <option value="">Semua Tahun</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="p-6">
            @if($configs->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($configs as $config)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 relative">
                            @if($config->is_active)
                                <span class="absolute top-4 right-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                            @endif
                            
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $config->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tahun {{ $config->period_year }}</p>
                            
                            <div class="mt-4 space-y-3">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Rasio Utama</p>
                                    <div class="flex gap-4 mt-1">
                                        <span class="text-sm font-medium text-blue-600">Jasa Pelayanan: {{ number_format($config->jasa_pelayanan_pct, 1) }}%</span>
                                        <span class="text-sm font-medium text-gray-600">Jasa Sarana: {{ number_format($config->jasa_sarana_pct, 1) }}%</span>
                                    </div>
                                </div>
                                
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Distribusi Internal</p>
                                    <div class="grid grid-cols-2 gap-2 mt-1 text-sm">
                                        <span>Medis: {{ number_format($config->pct_medis, 1) }}%</span>
                                        <span>Keperawatan: {{ number_format($config->pct_keperawatan, 1) }}%</span>
                                        <span>Penunjang: {{ number_format($config->pct_penunjang, 1) }}%</span>
                                        <span>Manajemen: {{ number_format($config->pct_manajemen, 1) }}%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 flex justify-between items-center">
                                <span class="text-xs text-gray-500">{{ $config->indexes->count() }} indeks jasa</span>
                                <div class="flex gap-2">
                                    <a href="{{ route('service-fees.configs.show', $config) }}" class="text-gray-600 hover:text-gray-900" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('service-fees.configs.edit', $config) }}" class="text-biru-dongker-600 hover:text-biru-dongker-900" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $configs->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Belum ada konfigurasi</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Buat konfigurasi untuk mengatur pembagian jasa.</p>
                    <div class="mt-6">
                        <a href="{{ route('service-fees.configs.create') }}" class="btn-primary">Tambah Konfigurasi</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
