@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                {{ $isEditMode ? 'Edit' : 'Tambah' }} Standard Resource Usage
            </h2>
            <a href="{{ route('standard-resource-usages.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                Kembali ke Daftar
            </a>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ $isEditMode ? route('standard-resource-usages.update', $serviceId) : route('standard-resource-usages.store') }}" method="POST" id="sru-form">
                    @csrf
                    @if($isEditMode)
                        @method('PUT')
                    @endif
                    
                    <!-- Bagian Service -->
                    <div class="mb-8 pb-6 border-b border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Service (Tindakan/Pemeriksaan)</h4>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                            <div class="col-span-12 md:col-span-6">
                                <label for="service_id" class="block text-sm font-medium text-gray-700">Nama Service <span class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <select id="service_id" name="service_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700" onchange="syncServiceSelection()">
                                        <option value="">{{ __('Select Service') }}</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}"
                                                data-name="{{ $service->service_description }}"
                                                data-code="{{ $service->service_code }}"
                                                data-category="{{ $service->category ?? '' }}"
                                                {{ old('service_id', $serviceData['service_id'] ?? '') == $service->id ? 'selected' : '' }}>
                                                {{ $service->service_description }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="service_name" name="service_name" value="{{ old('service_name', $serviceData['service_name'] ?? '') }}">
                                    @error('service_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    @error('service_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        Pilih service dari Cost Reference yang sudah terdaftar.
                                    </p>
                                </div>
                            </div>

                            <div class="col-span-12 md:col-span-6">
                                <label for="service_code" class="block text-sm font-medium text-gray-700">Service Code</label>
                                <div class="mt-1">
                                    <input
                                        type="text"
                                        id="service_code"
                                        name="service_code"
                                        value="{{ old('service_code', $serviceData['service_code'] ?? '') }}"
                                        maxlength="100"
                                        class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700"
                                    >
                                    @error('service_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        Akan otomatis terisi jika memilih dari master. Bisa diubah/manual jika diperlukan.
                                    </p>
                                </div>
                            </div>

                            <div class="col-span-12 md:col-span-6">
                                <label for="category" class="block text-sm font-medium text-gray-700">{{ __('Category') }} <span class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <select id="category" name="category" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                        <option value="">{{ __('Select Category') }}</option>
                                        <option value="barang" {{ old('category', $serviceData['category'] ?? '') == 'barang' ? 'selected' : '' }}>{{ __('Obat/BHP') }}</option>
                                        <option value="tindakan_rj" {{ old('category', $serviceData['category'] ?? '') == 'tindakan_rj' ? 'selected' : '' }}>{{ __('Tindakan Rawat Jalan') }}</option>
                                        <option value="tindakan_ri" {{ old('category', $serviceData['category'] ?? '') == 'tindakan_ri' ? 'selected' : '' }}>{{ __('Tindakan Rawat Inap') }}</option>
                                        <option value="laboratorium" {{ old('category', $serviceData['category'] ?? '') == 'laboratorium' ? 'selected' : '' }}>{{ __('Laboratorium') }}</option>
                                        <option value="radiologi" {{ old('category', $serviceData['category'] ?? '') == 'radiologi' ? 'selected' : '' }}>{{ __('Radiologi') }}</option>
                                        <option value="operasi" {{ old('category', $serviceData['category'] ?? '') == 'operasi' ? 'selected' : '' }}>{{ __('Operasi') }}</option>
                                        <option value="kamar" {{ old('category', $serviceData['category'] ?? '') == 'kamar' ? 'selected' : '' }}>{{ __('Kamar') }}</option>
                                    </select>
                                    @error('category')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-span-12">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $serviceData['is_active'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-biru-dongker-600 shadow-sm focus:border-biru-dongker-300 focus:ring focus:ring-biru-dongker-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian BMHP (Tabel Dinamis) -->
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-md font-semibold text-gray-900">Daftar BMHP</h4>
                            <button type="button" onclick="addBmhpRow()" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah BMHP
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="bmhp-table">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BMHP</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="bmhp-tbody" class="bg-white divide-y divide-gray-200">
                                    @if($isEditMode && isset($bmhpItems) && count($bmhpItems) > 0)
                                        @foreach($bmhpItems as $index => $item)
                                            <tr class="bmhp-row" data-row-index="{{ $index }}">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <select name="bmhp_items[{{ $index }}][bmhp_id]" class="bmhp-select block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm" required onchange="updateBmhpRow(this)">
                                                        <option value="">Pilih BMHP</option>
                                                        @foreach($bmhpList as $bmhp)
                                                            <option value="{{ $bmhp->id }}" 
                                                                data-price="{{ $bmhp->purchase_price ?? $bmhp->standard_cost ?? 0 }}"
                                                                data-code="{{ $bmhp->service_code }}"
                                                                data-desc="{{ $bmhp->service_description }}"
                                                                {{ $item['bmhp_id'] == $bmhp->id ? 'selected' : '' }}>
                                                                {{ $bmhp->service_code }} - {{ $bmhp->service_description }}
                                                                @if($bmhp->purchase_price || $bmhp->standard_cost)
                                                                    (Rp {{ number_format($bmhp->purchase_price ?? $bmhp->standard_cost, 0, ',', '.') }})
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number" name="bmhp_items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}" min="0.01" step="0.01" required class="bmhp-quantity block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm" onchange="calculateRowTotal(this)">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @if($uoms->count() > 0)
                                                        <select name="bmhp_items[{{ $index }}][unit_of_measurement_id]" id="unit-select-{{ $index }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm" required>
                                                            <option value="">-- Pilih Satuan --</option>
                                                            @foreach($uoms as $uom)
                                                                <option value="{{ $uom->id }}" {{ ($item['unit_of_measurement_id'] ?? '') == $uom->id ? 'selected' : '' }}>
                                                                    {{ $uom->name }} ({{ $uom->symbol }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="mt-2" style="display: none;">
                                                            <label for="unit-input-{{ $index }}" class="block text-xs text-gray-500">Manual Input (jika tidak ada di list):</label>
                                                            <input type="text" id="unit-input-{{ $index }}" name="bmhp_items[{{ $index }}][unit]" value="{{ $item['unit'] }}" maxlength="50" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm mt-1">
                                                        </div>
                                                        <a href="{{ route('units-of-measurement.create') }}" target="_blank" class="text-xs text-biru-dongker-600 hover:text-biru-dongker-800 mt-1 inline-block">
                                                            + Tambah Satuan Baru
                                                        </a>
                                                    @else
                                                        <input type="text" name="bmhp_items[{{ $index }}][unit]" value="{{ $item['unit'] }}" list="unit-suggestions-{{ $index }}" maxlength="50" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm">
                                                        <datalist id="unit-suggestions-{{ $index }}">
                                                            <option value="pcs">
                                                            <option value="ml">
                                                            <option value="mg">
                                                            <option value="gr">
                                                            <option value="kg">
                                                            <option value="unit">
                                                            <option value="botol">
                                                            <option value="vial">
                                                            <option value="ampul">
                                                            <option value="tablet">
                                                            <option value="kapsul">
                                                        </datalist>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                                    <span class="row-total font-semibold text-gray-900">Rp 0</span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    <button type="button" onclick="removeBmhpRow({{ $index }})" class="text-red-600 hover:text-red-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <!-- Baris kosong untuk template -->
                                        <tr class="bmhp-row" data-row-index="0" style="display: none;">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <select name="bmhp_items[0][bmhp_id]" class="bmhp-select block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm" onchange="updateBmhpRow(this)">
                                                    <option value="">Pilih BMHP</option>
                                                    @foreach($bmhpList as $bmhp)
                                                        <option value="{{ $bmhp->id }}" 
                                                            data-price="{{ $bmhp->purchase_price ?? $bmhp->standard_cost ?? 0 }}"
                                                            data-code="{{ $bmhp->service_code }}"
                                                            data-desc="{{ $bmhp->service_description }}">
                                                            {{ $bmhp->service_code }} - {{ $bmhp->service_description }}
                                                            @if($bmhp->purchase_price || $bmhp->standard_cost)
                                                                (Rp {{ number_format($bmhp->purchase_price ?? $bmhp->standard_cost, 0, ',', '.') }})
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="number" name="bmhp_items[0][quantity]" value="1" min="0.01" step="0.01" class="bmhp-quantity block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm" onchange="calculateRowTotal(this)">
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($uoms->count() > 0)
                                                    <select name="bmhp_items[0][unit_of_measurement_id]" class="unit-select block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm" required>
                                                        <option value="">-- Pilih Satuan --</option>
                                                        @foreach($uoms as $uom)
                                                            <option value="{{ $uom->id }}">
                                                                {{ $uom->name }} ({{ $uom->symbol }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    
                                                    <!-- Fallback text input (hidden by default unless JS logic changes or used as backup) -->
                                                    <input type="text" name="bmhp_items[0][unit]" class="unit-input hidden w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm mt-1" placeholder="Manual unit" maxlength="50">
                                                    
                                                    <a href="{{ route('units-of-measurement.create') }}" target="_blank" class="text-xs text-biru-dongker-600 hover:text-biru-dongker-800 mt-1 inline-block">
                                                        + Tambah Satuan Baru
                                                    </a>
                                                @else
                                                    <input type="text" name="bmhp_items[0][unit]" value="pcs" list="unit-suggestions-0" maxlength="50" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm">
                                                    <datalist id="unit-suggestions-0">
                                                        <option value="pcs">
                                                        <option value="ml">
                                                        <option value="mg">
                                                        <option value="gr">
                                                        <option value="kg">
                                                        <option value="unit">
                                                        <option value="botol">
                                                        <option value="vial">
                                                        <option value="ampul">
                                                        <option value="tablet">
                                                        <option value="kapsul">
                                                    </datalist>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                                <span class="row-total font-semibold text-gray-900">Rp 0</span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                                <button type="button" onclick="removeBmhpRow(0)" class="text-red-600 hover:text-red-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @if(!$isEditMode || !isset($bmhpItems) || count($bmhpItems) == 0)
                            <div id="empty-bmhp-message" class="mt-4 text-center text-gray-500 text-sm">
                                Belum ada BMHP. Klik tombol "Tambah BMHP" untuk menambahkan.
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                            {{ $isEditMode ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let rowIndexCounter = {{ $isEditMode && isset($bmhpItems) ? count($bmhpItems) : 0 }};

function syncServiceSelection() {
    const serviceSelect = document.getElementById('service_id');
    const serviceNameInput = document.getElementById('service_name');
    const serviceCodeInput = document.getElementById('service_code');
    const categorySelect = document.getElementById('category');

    const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const serviceName = selectedOption.getAttribute('data-name');
        const serviceCode = selectedOption.getAttribute('data-code');
        const category = selectedOption.getAttribute('data-category');
        
        // Update hidden service_name field
        if (serviceNameInput) {
            serviceNameInput.value = serviceName || '';
        }
        
        // Auto-fill service_code
        if (serviceCode && serviceCodeInput) {
            serviceCodeInput.value = serviceCode;
        }
        
            // Auto-fill category (wajib diisi, jadi selalu update jika ada dari service)
            if (category && categorySelect) {
                categorySelect.value = category;
            }
    } else {
        // Reset jika tidak ada yang dipilih
        if (serviceNameInput) {
            serviceNameInput.value = '';
        }
        if (serviceCodeInput) {
            serviceCodeInput.value = '';
        }
    }
}

function addBmhpRow() {
    const tbody = document.getElementById('bmhp-tbody');
    const emptyMessage = document.getElementById('empty-bmhp-message');
    if (emptyMessage) emptyMessage.style.display = 'none';

    // Clone template row (baris pertama yang hidden)
    const templateRow = tbody.querySelector('.bmhp-row');
    const newRow = templateRow.cloneNode(true);
    newRow.style.display = '';
    newRow.setAttribute('data-row-index', rowIndexCounter);

    // Update semua name attributes dengan index baru
    newRow.querySelectorAll('[name]').forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            input.setAttribute('name', name.replace(/\[0\]/, `[${rowIndexCounter}]`));
        }
    });

    // Update datalist id if exists
    const datalist = newRow.querySelector('datalist');
    if (datalist) {
        datalist.id = `unit-suggestions-${rowIndexCounter}`;
        const unitInput = newRow.querySelector('input[name*="[unit]"]');
        if (unitInput) {
            unitInput.setAttribute('list', `unit-suggestions-${rowIndexCounter}`);
        }
    }
    
    // Reset selections for new row
    const uomSelect = newRow.querySelector('select[name*="[unit_of_measurement_id]"]');
    if (uomSelect) {
        uomSelect.selectedIndex = 0;
    }

    // Reset values
    const select = newRow.querySelector('.bmhp-select');
    const qtyInput = newRow.querySelector('.bmhp-quantity');
    const unitInput = newRow.querySelector('input[name*="[unit]"]');
    const totalSpan = newRow.querySelector('.row-total');

    if (select) {
        select.value = '';
        select.setAttribute('required', 'required');
    }
    if (qtyInput) {
        qtyInput.value = '1';
        qtyInput.setAttribute('required', 'required');
    }
    if (unitInput) {
        unitInput.value = 'pcs';
        unitInput.setAttribute('required', 'required');
    }
    if (totalSpan) {
        totalSpan.textContent = 'Rp 0';
    }

    tbody.appendChild(newRow);
    rowIndexCounter++;
}

function removeBmhpRow(index) {
    const row = document.querySelector(`.bmhp-row[data-row-index="${index}"]`);
    if (row) {
        row.remove();
        updateEmptyMessage();
    }
}

function getRowFromArg(arg) {
    if (typeof arg === 'number' || typeof arg === 'string') {
        return document.querySelector(`.bmhp-row[data-row-index="${arg}"]`);
    }
    if (arg && arg.closest) {
        return arg.closest('.bmhp-row');
    }
    return null;
}

function updateBmhpRow(arg) {
    const row = getRowFromArg(arg);
    if (!row) return;
    const select = row.querySelector('.bmhp-select');
    const selectedOption = select ? select.options[select.selectedIndex] : null;
    if (selectedOption && selectedOption.value) {
        calculateRowTotal(row);
    } else {
        const totalSpan = row.querySelector('.row-total');
        if (totalSpan) totalSpan.textContent = 'Rp 0';
    }
}

function calculateRowTotal(arg) {
    const row = getRowFromArg(arg);
    if (!row) return;

    const select = row.querySelector('.bmhp-select');
    const quantityInput = row.querySelector('.bmhp-quantity');
    const totalSpan = row.querySelector('.row-total');

    const selectedOption = select ? select.options[select.selectedIndex] : null;
    const price = parseFloat(selectedOption?.getAttribute('data-price')) || 0;
    const quantity = parseFloat(quantityInput.value) || 0;

    const total = price * quantity;
    totalSpan.textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function updateEmptyMessage() {
    const tbody = document.getElementById('bmhp-tbody');
    const emptyMessage = document.getElementById('empty-bmhp-message');
    const visibleRows = tbody.querySelectorAll('.bmhp-row:not([style*="display: none"])');
    
    if (visibleRows.length === 0 && emptyMessage) {
        emptyMessage.style.display = 'block';
    } else if (emptyMessage) {
        emptyMessage.style.display = 'none';
    }
}

// Initialize calculations for existing rows
document.addEventListener('DOMContentLoaded', function() {
    // Sync service selection on page load (for edit mode)
    syncServiceSelection();
    
    document.querySelectorAll('.bmhp-row').forEach(row => {
        const index = row.getAttribute('data-row-index');
        if (index !== null && row.style.display !== 'none') {
            calculateRowTotal(index);
        }
    });
    
    // Add first row if creating new
    @if(!$isEditMode || !isset($bmhpItems) || count($bmhpItems) == 0)
        addBmhpRow();
    @endif
});
</script>
@endsection
