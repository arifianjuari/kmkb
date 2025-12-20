@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Tambah Data Biaya') }}</h2>
        <p class="text-sm text-gray-600">Tambahkan data biaya alat rumah tangga per cost center.</p>
    </div>

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('household-expenses.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div>
                <label for="cost_center_id" class="block text-sm font-medium text-gray-700">Cost Center <span class="text-red-500">*</span></label>
                <select name="cost_center_id" id="cost_center_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm">
                    <option value="">Pilih Cost Center</option>
                    @foreach($costCenters as $cc)
                        <option value="{{ $cc->id }}" {{ old('cost_center_id', $selectedCostCenter) == $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
                    @endforeach
                </select>
                @error('cost_center_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="household_item_id" class="block text-sm font-medium text-gray-700">Item <span class="text-red-500">*</span></label>
                <select name="household_item_id" id="household_item_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm" onchange="fillDefaultPrice(this)">
                    <option value="">Pilih Item</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" data-price="{{ $item->default_price }}" data-unit="{{ $item->unit }}" {{ old('household_item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }} ({{ $item->unit }})</option>
                    @endforeach
                </select>
                @error('household_item_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Belum ada item? <a href="{{ route('household-items.create') }}" class="text-biru-dongker-600 hover:underline">Tambah di Master Item</a></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="period_month" class="block text-sm font-medium text-gray-700">Bulan <span class="text-red-500">*</span></label>
                    <select name="period_month" id="period_month" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ old('period_month', $selectedMonth) == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                    @error('period_month')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="period_year" class="block text-sm font-medium text-gray-700">Tahun <span class="text-red-500">*</span></label>
                    <select name="period_year" id="period_year" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm">
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ old('period_year', $selectedYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    @error('period_year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" step="0.01" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm" placeholder="Contoh: 10" oninput="calculateTotal()">
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="unit_price" class="block text-sm font-medium text-gray-700">Harga Satuan <span class="text-red-500">*</span></label>
                    <input type="number" name="unit_price" id="unit_price" value="{{ old('unit_price') }}" step="0.01" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm" placeholder="Contoh: 18000" oninput="calculateTotal()">
                    @error('unit_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Total Biaya:</span>
                    <span id="totalDisplay" class="text-xl font-bold text-biru-dongker-600">Rp 0</span>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('household-expenses.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-4 py-2 bg-biru-dongker-600 text-white rounded-md text-sm hover:bg-biru-dongker-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function fillDefaultPrice(select) {
    const option = select.options[select.selectedIndex];
    const defaultPrice = option.dataset.price;
    if (defaultPrice && defaultPrice !== '') {
        document.getElementById('unit_price').value = defaultPrice;
        calculateTotal();
    }
}

function calculateTotal() {
    const qty = parseFloat(document.getElementById('quantity').value) || 0;
    const price = parseFloat(document.getElementById('unit_price').value) || 0;
    const total = qty * price;
    document.getElementById('totalDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
@endsection
