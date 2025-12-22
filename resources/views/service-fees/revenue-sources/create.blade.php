@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Tambah Sumber Pendapatan</h2>
        </div>

        <form action="{{ route('service-fees.revenue-sources.store') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode <span class="text-red-500">*</span></label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           placeholder="Contoh: bpjs, umum, asuransi">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           placeholder="Contoh: BPJS/JKN">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea id="description" name="description" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Urutan</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="flex items-center">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-biru-dongker-600 focus:ring-biru-dongker-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Aktif</label>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('service-fees.revenue-sources.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</section>
@endsection
