@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Edit Item') }}</h2>
        <p class="text-sm text-gray-600">Perbarui informasi item alat rumah tangga.</p>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('household-items.update', $householdItem) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="code" class="block text-sm font-medium text-gray-700">Kode (Opsional)</label>
                <input type="text" name="code" id="code" value="{{ old('code', $householdItem->code) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm" placeholder="Contoh: AMG">
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Item <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $householdItem->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm" placeholder="Contoh: Air Minum Galon">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="unit_of_measurement_id" class="block text-sm font-medium text-gray-700">Satuan <span class="text-red-500">*</span></label>
                <select name="unit_of_measurement_id" id="unit_of_measurement_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm" onchange="toggleCustomUnit(this)">
                    <option value="">-- Pilih dari daftar atau ketik manual --</option>
                    @foreach($unitsOfMeasurement as $uom)
                        <option value="{{ $uom->id }}" {{ old('unit_of_measurement_id', $householdItem->unit_of_measurement_id) == $uom->id ? 'selected' : '' }}>{{ $uom->display_name }}</option>
                    @endforeach
                    <option value="custom" {{ !$householdItem->unit_of_measurement_id && $householdItem->unit ? 'selected' : '' }}>✏️ Ketik satuan manual...</option>
                </select>
                @error('unit_of_measurement_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="customUnitContainer" class="{{ !$householdItem->unit_of_measurement_id && $householdItem->unit ? '' : 'hidden' }}">
                <label for="unit" class="block text-sm font-medium text-gray-700">Satuan Manual</label>
                <input type="text" name="unit" id="unit" value="{{ old('unit', $householdItem->unit) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm" placeholder="Contoh: Galon, Box, Pak">
                @error('unit')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="default_price" class="block text-sm font-medium text-gray-700">Harga Default (Opsional)</label>
                <input type="number" name="default_price" id="default_price" value="{{ old('default_price', $householdItem->default_price) }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm" placeholder="Contoh: 18000">
                @error('default_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $householdItem->is_active) ? 'checked' : '' }} class="h-4 w-4 text-biru-dongker-600 focus:ring-biru-dongker-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">Item Aktif</label>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('household-items.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-4 py-2 bg-biru-dongker-800 text-white rounded-md text-sm hover:bg-biru-dongker-900">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleCustomUnit(select) {
    const customContainer = document.getElementById('customUnitContainer');
    const unitInput = document.getElementById('unit');
    
    if (select.value === 'custom' || select.value === '') {
        customContainer.classList.remove('hidden');
        if (select.value === 'custom') {
            unitInput.required = true;
        }
    } else {
        customContainer.classList.add('hidden');
        unitInput.required = false;
    }
}
</script>
@endsection
