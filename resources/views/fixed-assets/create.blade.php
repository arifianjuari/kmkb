@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Tambah Aset Baru') }}</h2>
        <a href="{{ route('fixed-assets.index') }}" class="btn-secondary">{{ __('Kembali') }}</a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('fixed-assets.store') }}" method="POST">
                @csrf
                
                <!-- Basic Info -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="asset_code" class="block text-sm font-medium text-gray-700">Kode Aset <span class="text-red-500">*</span></label>
                            <input type="text" name="asset_code" id="asset_code" value="{{ old('asset_code') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                            @error('asset_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Aset <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="asset_category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                            <select name="asset_category_id" id="asset_category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" data-useful-life="{{ $cat->default_useful_life_years }}" {{ old('asset_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="cost_center_id" class="block text-sm font-medium text-gray-700">Cost Center</label>
                            <select name="cost_center_id" id="cost_center_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                                <option value="">-- Pilih Cost Center --</option>
                                @foreach($costCenters as $cc)
                                    <option value="{{ $cc->id }}" {{ old('cost_center_id') == $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" id="description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Inventory Info -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Inventaris</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700">Merk/Brand</label>
                            <input type="text" name="brand" id="brand" value="{{ old('brand') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700">Model/Tipe</label>
                            <input type="text" name="model" id="model" value="{{ old('model') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="serial_number" class="block text-sm font-medium text-gray-700">Nomor Seri</label>
                            <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="manufacturer" class="block text-sm font-medium text-gray-700">Produsen</label>
                            <input type="text" name="manufacturer" id="manufacturer" value="{{ old('manufacturer') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700">Lokasi Fisik</label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="condition" class="block text-sm font-medium text-gray-700">Kondisi</label>
                            <select name="condition" id="condition" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                                @foreach($conditionOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('condition', 'good') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Financial Info -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Keuangan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="acquisition_date" class="block text-sm font-medium text-gray-700">Tanggal Perolehan <span class="text-red-500">*</span></label>
                            <input type="date" name="acquisition_date" id="acquisition_date" value="{{ old('acquisition_date') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                            @error('acquisition_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="acquisition_cost" class="block text-sm font-medium text-gray-700">Harga Perolehan <span class="text-red-500">*</span></label>
                            <input type="number" name="acquisition_cost" id="acquisition_cost" value="{{ old('acquisition_cost') }}" min="0" step="1" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                            @error('acquisition_cost')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="useful_life_years" class="block text-sm font-medium text-gray-700">Umur Ekonomis (Tahun) <span class="text-red-500">*</span></label>
                            <input type="number" name="useful_life_years" id="useful_life_years" value="{{ old('useful_life_years', 4) }}" min="1" max="100" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                            @error('useful_life_years')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="salvage_value" class="block text-sm font-medium text-gray-700">Nilai Sisa</label>
                            <input type="number" name="salvage_value" id="salvage_value" value="{{ old('salvage_value', 0) }}" min="0" step="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="warranty_end_date" class="block text-sm font-medium text-gray-700">Tanggal Berakhir Garansi</label>
                            <input type="date" name="warranty_end_date" id="warranty_end_date" value="{{ old('warranty_end_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                        <div>
                            <label for="calibration_due_date" class="block text-sm font-medium text-gray-700">Tanggal Kalibrasi</label>
                            <input type="date" name="calibration_due_date" id="calibration_due_date" value="{{ old('calibration_due_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">{{ __('Simpan') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('asset_category_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const usefulLife = selected.dataset.usefulLife;
    if (usefulLife) {
        document.getElementById('useful_life_years').value = usefulLife;
    }
});
</script>
@endpush
@endsection
