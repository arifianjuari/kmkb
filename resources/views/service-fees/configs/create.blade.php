@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Tambah Konfigurasi Jasa</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Atur rasio pembagian jasa sesuai kebijakan rumah sakit</p>
        </div>

        <form action="{{ route('service-fees.configs.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Konfigurasi <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm"
                           placeholder="Contoh: Konfigurasi 2025">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="period_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun <span class="text-red-500">*</span></label>
                    <input type="number" id="period_year" name="period_year" value="{{ old('period_year', date('Y')) }}" required min="2020" max="2099"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                </div>
            </div>

            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Rasio Utama (Permenkes 85/2015)</h3>
                <p class="text-sm text-gray-500 mb-4">Total Jasa Pelayanan + Jasa Sarana harus = 100%</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="jasa_pelayanan_pct" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Jasa Pelayanan (SDM) <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-500">(Maks 44%)</span>
                        </label>
                        <div class="mt-1 relative">
                            <input type="number" id="jasa_pelayanan_pct" name="jasa_pelayanan_pct" value="{{ old('jasa_pelayanan_pct', 44) }}" required step="0.01" min="0" max="100"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm pr-8">
                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
                        </div>
                        @error('jasa_pelayanan_pct')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jasa_sarana_pct" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Jasa Sarana (RS) <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-500">(Min 56%)</span>
                        </label>
                        <div class="mt-1 relative">
                            <input type="number" id="jasa_sarana_pct" name="jasa_sarana_pct" value="{{ old('jasa_sarana_pct', 56) }}" required step="0.01" min="0" max="100"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm pr-8">
                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Distribusi Jasa Pelayanan</h3>
                <p class="text-sm text-gray-500 mb-4">Total distribusi (Medis + Keperawatan + Penunjang + Manajemen) harus = 100%</p>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <label for="pct_medis" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jasa Medis <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative">
                            <input type="number" id="pct_medis" name="pct_medis" value="{{ old('pct_medis', 60) }}" required step="0.01" min="0" max="100"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm pr-8">
                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
                        </div>
                        @error('pct_medis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pct_keperawatan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jasa Keperawatan <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative">
                            <input type="number" id="pct_keperawatan" name="pct_keperawatan" value="{{ old('pct_keperawatan', 25) }}" required step="0.01" min="0" max="100"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm pr-8">
                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
                        </div>
                    </div>

                    <div>
                        <label for="pct_penunjang" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jasa Penunjang <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative">
                            <input type="number" id="pct_penunjang" name="pct_penunjang" value="{{ old('pct_penunjang', 10) }}" required step="0.01" min="0" max="100"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm pr-8">
                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
                        </div>
                    </div>

                    <div>
                        <label for="pct_manajemen" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jasa Manajemen <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative">
                            <input type="number" id="pct_manajemen" name="pct_manajemen" value="{{ old('pct_manajemen', 5) }}" required step="0.01" min="0" max="100"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm pr-8">
                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
                <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">{{ old('notes') }}</textarea>
            </div>

            <div class="mt-4">
                <input type="hidden" name="is_active" value="0">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-biru-dongker-600 focus:ring-biru-dongker-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktifkan konfigurasi ini</span>
                </label>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('service-fees.configs.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</section>
@endsection
