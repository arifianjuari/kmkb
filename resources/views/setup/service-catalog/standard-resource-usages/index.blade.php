@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">Standard Resource Usage</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('sru-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="Apa itu Standard Resource Usage?"
                    title="Apa itu Standard Resource Usage?"
                >
                    i
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <button id="sync-selected" class="hidden inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Sync Selected
                </button>
                <a href="{{ route('standard-resource-usages.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Export Excel
                </a>
                <a href="{{ route('standard-resource-usages.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    Tambah Baru
                </a>
            </div>
        </div>

        <div id="sru-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Standard Resource Usage</span> adalah fitur untuk menyimpan standar pemakaian BMHP (Barang Medis Habis Pakai) untuk setiap 1 kali tindakan/pemeriksaan.
            </p>
            <div class="mb-2">
                <p class="font-semibold mb-1">Konsep:</p>
                <p class="mb-2">Seperti "resep biaya" atau bill of material untuk tindakan klinis. Contoh: untuk 1x pasang infus dewasa diperlukan BMHP apa saja dan berapa banyak.</p>
                <p class="font-semibold mb-1">Komponen:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li><strong>Service</strong>: Tindakan atau pemeriksaan yang memerlukan BMHP</li>
                    <li><strong>BMHP</strong>: Barang Medis Habis Pakai yang digunakan</li>
                    <li><strong>Quantity</strong>: Jumlah BMHP yang diperlukan per 1x tindakan</li>
                    <li><strong>Unit</strong>: Satuan BMHP (pcs, ml, mg, dll)</li>
                    <li><strong>Total Cost</strong>: Biaya total BMHP (Quantity × Harga BMHP)</li>
                </ul>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('standard-resource-usages.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Cari service atau BMHP..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm">
                </div>
                <div>
                    <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                    <select name="service_id" id="service_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm">
                        <option value="">Semua</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ $serviceId == $service->id ? 'selected' : '' }}>
                                {{ $service->service_description }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="bmhp_id" class="block text-sm font-medium text-gray-700 mb-1">BMHP</label>
                    <select name="bmhp_id" id="bmhp_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm">
                        <option value="">Semua</option>
                        @foreach($bmhpList as $bmhp)
                            <option value="{{ $bmhp->id }}" {{ $bmhpId == $bmhp->id ? 'selected' : '' }}>
                                {{ $bmhp->service_description }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="is_active" id="is_active" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-500 focus:ring-biru-dongker-500 sm:text-sm">
                        <option value="">Semua</option>
                        <option value="1" {{ $isActive === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ $isActive === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                        Filter
                    </button>
                    <a href="{{ route('standard-resource-usages.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @php
                    $categoryTabs = [
                        'all' => 'Semua Kategori',
                        'barang' => 'Obat/BHP',
                        'tindakan_rj' => 'Tindakan RJ',
                        'tindakan_ri' => 'Tindakan RI',
                        'laboratorium' => 'Laboratorium',
                        'radiologi' => 'Radiologi',
                        'operasi' => 'Operasi',
                        'kamar' => 'Kamar',
                    ];
                @endphp

                {{-- Category Tabs --}}
                <div class="mb-6">
                    <p class="text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider">Category</p>
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach($categoryTabs as $key => $label)
                            @php
                                $isActiveTab = ($key === 'all' && !$category) || ($key === $category);
                                $urlParams = request()->except('category', 'page');
                                if ($key !== 'all') {
                                    $urlParams['category'] = $key;
                                }
                                $tabUrl = route('standard-resource-usages.index', $urlParams);
                            @endphp
                            <a
                                href="{{ $tabUrl }}"
                                class="inline-flex items-center gap-2 px-4 py-2 border rounded-full text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-biru-dongker-700 {{ $isActiveTab ? 'bg-biru-dongker-800 text-white border-biru-dongker-800' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}"
                            >
                                <span>{{ $label }}</span>
                                @if(isset($categoryCounts))
                                    <span class="text-xs font-semibold {{ $isActiveTab ? 'text-white/80' : 'text-gray-500' }}">
                                        {{ $categoryCounts[$key] ?? 0 }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                @if($standardResourceUsages->count() > 0)
                    @php
                        // Grouping per service supaya tampilan tidak terasa terpisah-pisah
                        $grouped = $standardResourceUsages->groupBy(function($item) {
                            return $item->service_id ?: ($item->service_name . '|' . $item->service_code);
                        });
                    @endphp
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-8">
                                        <input type="checkbox" id="select-all-sru" class="rounded border-gray-300 text-biru-dongker-800 shadow-sm focus:border-biru-dongker-500 focus:ring focus:ring-biru-dongker-400 focus:ring-opacity-50">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BMHP</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Grand Total</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($grouped as $groupKey => $group)
                                    @php
                                        $first = $group->first();
                                        // Hitung total biaya keseluruhan untuk service ini
                                        $totalBiayaKeseluruhan = $group->sum(function($item) {
                                            return $item->getTotalCost();
                                        });
                                        $bmhpCount = $group->count();
                                        $serviceId = 'service-' . md5($groupKey);
                                        $hasMultipleBmhp = $bmhpCount > 1;
                                    @endphp
                                    {{-- Baris Service Utama --}}
                                    <tr class="service-main-row bg-gray-50 hover:bg-gray-100" data-service-id="{{ $serviceId }}">
                                        <td class="px-2 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <input type="checkbox" class="row-checkbox rounded border-gray-300 text-biru-dongker-800 shadow-sm focus:border-biru-dongker-500 focus:ring focus:ring-biru-dongker-400 focus:ring-opacity-50" 
                                                    data-code-service="{{ $first->service->service_code ?? $first->service_code ?? '' }}" 
                                                    data-nama-service="{{ $first->service->service_description ?? $first->service_name ?? '' }}" 
                                                    data-grand-total="{{ $totalBiayaKeseluruhan }}"
                                                    data-category="{{ $first->category ?? ($first->service->category ?? '') }}">
                                                @if($hasMultipleBmhp)
                                                    <button type="button" onclick="toggleService('{{ $serviceId }}')" class="text-gray-600 hover:text-gray-900 focus:outline-none" title="Expand/Collapse">
                                                        <svg id="icon-{{ $serviceId }}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="font-medium">
                                                {{ $first->service->service_description ?? $first->service_name ?? '-' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $first->service->service_code ?? $first->service_code ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($hasMultipleBmhp)
                                                <span class="text-gray-500 italic">{{ $bmhpCount }} BMHP</span>
                                            @else
                                                <div class="font-medium">{{ $first->bmhp->service_description ?? '-' }}</div>
                                                <div class="text-xs text-gray-500">{{ $first->bmhp->service_code ?? '-' }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            @if(!$hasMultipleBmhp)
                                                {{ number_format($first->quantity, 2, ',', '.') }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if(!$hasMultipleBmhp)
                                                {{ $first->unit }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            @if(!$hasMultipleBmhp)
                                                Rp {{ number_format($first->getTotalCost(), 0, ',', '.') }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
                                            Rp {{ number_format($totalBiayaKeseluruhan, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($first->is_active)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Tidak Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('standard-resource-usages.edit', $first) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.888.96l2.685-.8a5.25 5.25 0 002.214-1.32l8.4-8.4z" />
                                                        <path d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('standard-resource-usages.destroy', $first) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA BMHP untuk service ini? Tindakan ini tidak dapat dibatalkan.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500" title="Hapus">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                            <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1Zm-3 6h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 9Zm3 2v8h2v-8H9Zm4 0v8h2v-8h-2Z"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- Baris Detail BMHP (hanya muncul jika lebih dari 1 BMHP) --}}
                                    @if($hasMultipleBmhp)
                                        @foreach($group as $index => $sru)
                                            <tr class="service-detail-row" data-service-id="{{ $serviceId }}" style="display: none;">
                                                <td class="px-2 py-4 whitespace-nowrap"></td>
                                                <td class="px-6 py-4 whitespace-nowrap"></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <div class="font-medium">{{ $sru->bmhp->service_description ?? '-' }}</div>
                                                    <div class="text-xs text-gray-500">{{ $sru->bmhp->service_code ?? '-' }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                                    {{ number_format($sru->quantity, 2, ',', '.') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $sru->unit }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                                    Rp {{ number_format($sru->getTotalCost(), 0, ',', '.') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap"></td>
                                                <td class="px-6 py-4 whitespace-nowrap"></td>
                                                <td class="px-6 py-4 whitespace-nowrap"></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $standardResourceUsages->links() }}
                    </div>
                @else
                    <p class="text-gray-600">Tidak ada data Standard Resource Usage ditemukan.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleService(serviceId) {
    const detailRows = document.querySelectorAll(`tr.service-detail-row[data-service-id="${serviceId}"]`);
    const icon = document.getElementById(`icon-${serviceId}`);
    
    if (!detailRows || detailRows.length === 0) {
        console.log('No detail rows found for service:', serviceId);
        return;
    }
    
    // Toggle display untuk setiap baris detail
    detailRows.forEach(row => {
        const isHidden = row.style.display === 'none' || !row.style.display;
        row.style.display = isHidden ? 'table-row' : 'none';
    });
    
    // Rotate icon
    if (icon) {
        const isExpanded = detailRows[0] && detailRows[0].style.display === 'table-row';
        if (isExpanded) {
            icon.classList.add('rotate-180');
        } else {
            icon.classList.remove('rotate-180');
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all-sru');
    const syncButton = document.getElementById('sync-selected');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    
    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function(e) {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = e.target.checked;
            });
            toggleSyncButton();
        });
    }
    
    // Individual checkbox change
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
            }
            toggleSyncButton();
        });
    });
    
    // Sync button event listener
    if (syncButton) {
        syncButton.addEventListener('click', syncSelectedItems);
    }
    
    // Update button state on page load
    toggleSyncButton();
});

function toggleSyncButton() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const syncButton = document.getElementById('sync-selected');
    
    if (syncButton) {
        if (selectedCheckboxes.length > 0) {
            syncButton.classList.remove('hidden');
        } else {
            syncButton.classList.add('hidden');
        }
    }
}

function syncSelectedItems() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        alert('Silakan pilih item yang ingin disinkronkan.');
        return;
    }
    
    // Prepare data for syncing
    const itemsToSync = [];
    selectedCheckboxes.forEach(checkbox => {
        itemsToSync.push({
            code_service: checkbox.dataset.codeService,
            nama_service: checkbox.dataset.namaService,
            grand_total: parseFloat(checkbox.dataset.grandTotal) || 0,
            category: checkbox.dataset.category || ''
        });
    });
    
    // Send data to server
    const syncButton = document.getElementById('sync-selected');
    syncButton.disabled = true;
    syncButton.textContent = 'Syncing...';
    
    fetch('/api/standard-resource-usages/sync-to-cost-references', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ items: itemsToSync }),
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`${data.synced_count} item berhasil disinkronkan ke Cost References.`);
            // Reset selection
            const selectAllCheckbox = document.getElementById('select-all-sru');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
            }
            selectedCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            toggleSyncButton();
        } else {
            alert('Gagal menyinkronkan data: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyinkronkan data.');
    })
    .finally(() => {
        syncButton.disabled = false;
        syncButton.textContent = 'Sync Selected';
    });
}
</script>
@endsection

