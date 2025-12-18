@extends('layouts.app')

@section('title', 'Service Volume (Current) - Kamar')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="grid gap-6 md:grid-cols-4">
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select id="year" name="year" class="w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700">
                    @foreach($availableYears as $yearOption)
                        <option value="{{ $yearOption }}" {{ (int) $year === (int) $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="bangsal" class="block text-sm font-medium text-gray-700 mb-1">Bangsal</label>
                <select id="bangsal" name="bangsal[]" multiple class="w-full h-36 rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700">
                    @foreach($bangsalOptions as $option)
                        <option value="{{ $option->kd_bangsal }}" {{ in_array($option->kd_bangsal, $bangsal ?? []) ? 'selected' : '' }}>
                            {{ $option->kd_bangsal }} - {{ $option->nm_bangsal }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Gunakan Ctrl/Command untuk memilih lebih dari satu.</p>
            </div>
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Kamar</label>
                <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Kode kamar, bangsal, kelas..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="inline-flex items-center justify-center w-full md:w-auto px-4 py-2 bg-biru-dongker-800 text-white rounded-md shadow hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    Terapkan Filter
                </button>
                <button type="submit"
                        formaction="{{ route('svc-current.kamar.export') }}"
                        class="inline-flex items-center justify-center w-full md:w-auto px-4 py-2 bg-emerald-600 text-white rounded-md shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    Export Excel
                </button>
            </div>
        </form>
    </div>

    @if($errorMessage)
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ $errorMessage }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Rekap Pemakaian Kamar (Dikelompokkan per Kelas)</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Tahun {{ $year }}
                        @if(!empty($bangsal))
                            • Bangsal {{ implode(', ', $bangsal) }}
                        @endif
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="sync-selected" class="hidden inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Sync ke Cost References
                    </button>
                    <button id="sync-to-volumes" class="hidden inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Sync ke Service Volumes
                    </button>
                    <p class="text-sm text-gray-500">Sumber data: SIMRS</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="w-10 px-3 py-3 text-left">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-biru-dongker-800 shadow-sm focus:border-biru-dongker-500 focus:ring focus:ring-biru-dongker-400 focus:ring-opacity-50">
                        </th>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700">Kode Kamar</th>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700">Bangsal</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700">Tarif (Rp)</th>
                        @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'] as $bulan)
                            <th scope="col" class="px-3 py-3 text-center font-semibold text-gray-700">{{ $bulan }}</th>
                        @endforeach
                        <th scope="col" class="px-4 py-3 text-center font-semibold text-gray-700 bg-rose-50">Total Hari</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700 bg-rose-50">Total Pendapatan (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        // Group data by kelas
                        $groupedData = collect($kamarData)->groupBy('kelas');
                    @endphp
                    
                    @forelse($groupedData as $kelas => $items)
                        @php
                            // Calculate subtotals for this group
                            $groupTotals = [
                                'jan' => $items->sum('jan'),
                                'feb' => $items->sum('feb'),
                                'mar' => $items->sum('mar'),
                                'apr' => $items->sum('apr'),
                                'may' => $items->sum('may'),
                                'jun' => $items->sum('jun'),
                                'jul' => $items->sum('jul'),
                                'aug' => $items->sum('aug'),
                                'sep' => $items->sum('sep'),
                                'oct' => $items->sum('oct'),
                                'nov' => $items->sum('nov'),
                                'dec' => $items->sum('dec'),
                                'total_hari' => $items->sum('total_hari'),
                                'total_pendapatan' => $items->sum('total_pendapatan'),
                            ];
                        @endphp
                        
                        {{-- Group Header --}}
                        <tr class="bg-blue-50">
                            <td colspan="18" class="px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-blue-900">
                                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Kelas: {{ $kelas ?: 'Tidak Diketahui' }}
                                    </span>
                                    <span class="text-sm text-blue-700">{{ $items->count() }} kamar</span>
                                </div>
                            </td>
                        </tr>
                        
                        {{-- Data Rows for this group --}}
                        @foreach($items as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="w-10 px-3 py-3">
                                    <input type="checkbox" class="row-checkbox rounded border-gray-300 text-biru-dongker-800 shadow-sm focus:border-biru-dongker-500 focus:ring focus:ring-biru-dongker-400 focus:ring-opacity-50"
                                           data-kode="{{ $row['kode'] }}"
                                           data-nama="{{ $row['bangsal'] }} - {{ $row['kelas'] }}"
                                           data-harga="{{ $row['tarif'] }}"
                                           data-jan="{{ $row['jan'] }}"
                                           data-feb="{{ $row['feb'] }}"
                                           data-mar="{{ $row['mar'] }}"
                                           data-apr="{{ $row['apr'] }}"
                                           data-may="{{ $row['may'] }}"
                                           data-jun="{{ $row['jun'] }}"
                                           data-jul="{{ $row['jul'] }}"
                                           data-aug="{{ $row['aug'] }}"
                                           data-sep="{{ $row['sep'] }}"
                                           data-oct="{{ $row['oct'] }}"
                                           data-nov="{{ $row['nov'] }}"
                                           data-dec="{{ $row['dec'] }}">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $row['kode'] }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $row['bangsal'] }}</td>
                                <td class="px-4 py-3 text-right text-gray-900">{{ number_format($row['tarif'], 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['jan'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['feb'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['mar'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['apr'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['may'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['jun'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['jul'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['aug'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['sep'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['oct'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['nov'] }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['dec'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold bg-rose-50">{{ $row['total_hari'] }}</td>
                                <td class="px-4 py-3 text-right font-semibold bg-rose-50">{{ number_format($row['total_pendapatan'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        
                        {{-- Group Subtotal --}}
                        <tr class="bg-blue-100 font-semibold">
                            <td class="px-3 py-2"></td>
                            <td class="px-4 py-2 text-blue-900" colspan="2">Subtotal Kelas {{ $kelas ?: 'Tidak Diketahui' }}</td>
                            <td class="px-4 py-2"></td>
                            <td class="px-3 py-2 text-center text-blue-900">{{ $groupTotals['jan'] }}</td>
                            <td class="px-3 py-2 text-center text-blue-900">{{ $groupTotals['feb'] }}</td>
                            <td class="px-3 py-2 text-center text-blue-900">{{ $groupTotals['mar'] }}</td>
                            <td class="px-3 py-2 text-center text-blue-900">{{ $groupTotals['apr'] }}</td>
                            <td class="px-3 py-2 text-center text-blue-900">{{ $groupTotals['may'] }}</td>
                            <td class="px-3 py-2 text-center text-blue-900">{{ $groupTotals['jun'] }}</td>
                            <td class="px-3 py-2 text-center text-blue-900">{{ $groupTotals['jul'] }}</td>
                            <td class="px-3 py-2 text-center text-blue-900">{{ $groupTotals['aug'] }}</td>
                            <td class="px-3 py-2 text-center text-blue-900">{{ $groupTotals['sep'] }}</td>
                            <td class="px-3 py-2 text-center text-blue-900">{{ $groupTotals['oct'] }}</td>
                            <td class="px-3 py-2 text-center text-blue-900">{{ $groupTotals['nov'] }}</td>
                            <td class="px-3 py-2 text-center text-blue-900">{{ $groupTotals['dec'] }}</td>
                            <td class="px-4 py-2 text-center text-blue-900 bg-blue-200">{{ $groupTotals['total_hari'] }}</td>
                            <td class="px-4 py-2 text-right text-blue-900 bg-blue-200">{{ number_format($groupTotals['total_pendapatan'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="18" class="px-4 py-6 text-center text-gray-500">Belum ada data kamar untuk filter yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($kamarData) > 0)
                    <tfoot class="bg-gray-100">
                        <tr class="font-bold">
                            <td class="px-3 py-3"></td>
                            <td class="px-4 py-3 text-gray-900" colspan="2">GRAND TOTAL (Semua Kelas)</td>
                            <td class="px-4 py-3"></td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['jan'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['feb'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['mar'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['apr'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['may'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['jun'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['jul'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['aug'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['sep'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['oct'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['nov'] }}</td>
                            <td class="px-3 py-3 text-center">{{ $grandTotals['dec'] }}</td>
                            <td class="px-4 py-3 text-center bg-rose-100">{{ $grandTotals['total_hari'] }}</td>
                            <td class="px-4 py-3 text-right bg-rose-100">{{ number_format($grandTotals['total_pendapatan'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const syncButton = document.getElementById('sync-selected');
    const syncVolumesButton = document.getElementById('sync-to-volumes');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    // Toggle sync buttons visibility based on selection
    function toggleSyncButtons() {
        const anyChecked = document.querySelector('.row-checkbox:checked');
        if (anyChecked) {
            syncButton.classList.remove('hidden');
            syncVolumesButton.classList.remove('hidden');
        } else {
            syncButton.classList.add('hidden');
            syncVolumesButton.classList.add('hidden');
        }
    }

    // Select all checkbox handler
    selectAllCheckbox.addEventListener('change', function() {
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        toggleSyncButtons();
    });

    // Individual checkbox handlers
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = document.querySelectorAll('.row-checkbox:checked').length === rowCheckboxes.length;
            selectAllCheckbox.checked = allChecked;
            toggleSyncButtons();
        });
    });

    // Sync to Cost References handler
    syncButton.addEventListener('click', function() {
        const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
        
        if (selectedCheckboxes.length === 0) {
            alert('Silakan pilih item yang ingin disinkronkan.');
            return;
        }

        const itemsToSync = [];
        selectedCheckboxes.forEach(checkbox => {
            itemsToSync.push({
                kode: checkbox.dataset.kode,
                nama: checkbox.dataset.nama,
                harga: checkbox.dataset.harga
            });
        });

        syncButton.disabled = true;
        syncButton.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Syncing...';

        fetch('{{ route("svc-current.kamar.sync") }}', {
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
                selectAllCheckbox.checked = false;
                selectedCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                toggleSyncButtons();
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
            syncButton.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Sync ke Cost References';
        });
    });

    // Sync to Service Volumes handler
    syncVolumesButton.addEventListener('click', function() {
        const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
        
        if (selectedCheckboxes.length === 0) {
            alert('Silakan pilih item yang ingin disinkronkan.');
            return;
        }

        const itemsToSync = [];
        selectedCheckboxes.forEach(checkbox => {
            itemsToSync.push({
                kode: checkbox.dataset.kode,
                nama: checkbox.dataset.nama,
                harga: checkbox.dataset.harga,
                jan: checkbox.dataset.jan,
                feb: checkbox.dataset.feb,
                mar: checkbox.dataset.mar,
                apr: checkbox.dataset.apr,
                may: checkbox.dataset.may,
                jun: checkbox.dataset.jun,
                jul: checkbox.dataset.jul,
                aug: checkbox.dataset.aug,
                sep: checkbox.dataset.sep,
                oct: checkbox.dataset.oct,
                nov: checkbox.dataset.nov,
                dec: checkbox.dataset.dec
            });
        });

        syncVolumesButton.disabled = true;
        syncVolumesButton.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Syncing...';

        fetch('{{ route("svc-current.kamar.sync-volumes") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ items: itemsToSync, year: {{ $year }}, kd_bangsal: '{{ isset($bangsal) && count($bangsal) > 0 ? $bangsal[0] : '' }}' }),
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`${data.synced_count} volume record berhasil disinkronkan ke Service Volumes.`);
                selectAllCheckbox.checked = false;
                selectedCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                toggleSyncButtons();
            } else {
                alert('Gagal menyinkronkan data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyinkronkan data.');
        })
        .finally(() => {
            syncVolumesButton.disabled = false;
            syncVolumesButton.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Sync ke Service Volumes';
        });
    });
});
</script>
@endsection
