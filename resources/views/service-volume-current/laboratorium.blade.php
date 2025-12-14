@extends('layouts.app')

@section('title', 'Service Volume (Current) - Laboratorium')

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
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Pasien</label>
                <select id="status" name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700">
                    <option value="all" {{ $status === null ? 'selected' : '' }}>Semua Status</option>
                    <option value="Ralan" {{ $status === 'Ralan' ? 'selected' : '' }}>Rawat Jalan</option>
                    <option value="Ranap" {{ $status === 'Ranap' ? 'selected' : '' }}>Rawat Inap</option>
                </select>
            </div>
            <div>
                <label for="poli" class="block text-sm font-medium text-gray-700 mb-1">Poliklinik</label>
                <select id="poli" name="poli" class="w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700">
                    <option value="">Semua Poli</option>
                    @foreach($poliOptions as $option)
                        <option value="{{ $option->kd_poli }}" {{ $poli === $option->kd_poli ? 'selected' : '' }}>
                            {{ $option->kd_poli }} - {{ $option->nm_poli }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Nama Pemeriksaan</label>
                <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Nama pemeriksaan..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700">
            </div>
            <div class="flex items-end space-x-2 md:col-span-4">
                <button type="submit" class="inline-flex items-center justify-center w-full md:w-auto px-4 py-2 bg-biru-dongker-800 text-white rounded-md shadow hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    Terapkan Filter
                </button>
                <button type="submit"
                        formaction="{{ route('svc-current.laboratorium.export') }}"
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
                    <h2 class="text-lg font-semibold text-gray-900">Rekap Pemeriksaan Laboratorium</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Tahun {{ $year }}
                        @if($status)
                            • Status {{ $status === 'Ralan' ? 'Rawat Jalan' : 'Rawat Inap' }}
                        @endif
                        @if($poli)
                            • Poli {{ $poli }}
                        @endif
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="sync-selected" class="hidden inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Sync Selected ke Cost References
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
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700">Nama Pemeriksaan</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700">Harga (Rp)</th>
                        @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'] as $bulan)
                            <th scope="col" class="px-3 py-3 text-center font-semibold text-gray-700">{{ $bulan }}</th>
                        @endforeach
                        <th scope="col" class="px-4 py-3 text-center font-semibold text-gray-700 bg-rose-50">Total Jumlah Pemeriksaan</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700 bg-rose-50">Total Pendapatan (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($laboratoriumData as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="w-10 px-3 py-3">
                                <input type="checkbox" class="row-checkbox rounded border-gray-300 text-biru-dongker-800 shadow-sm focus:border-biru-dongker-500 focus:ring focus:ring-biru-dongker-400 focus:ring-opacity-50"
                                       data-kode="{{ $row['kode'] }}"
                                       data-nama="{{ $row['nama'] }}"
                                       data-harga="{{ $row['harga'] }}">
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $row['nama'] }}</div>
                                <div class="text-xs text-gray-500">Kode: {{ $row['kode'] }}</div>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($row['harga'], 0, ',', '.') }}</td>
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
                            <td class="px-4 py-3 text-center font-semibold bg-rose-50">{{ $row['total_tindakan'] }}</td>
                            <td class="px-4 py-3 text-right font-semibold bg-rose-50">{{ number_format($row['total_pendapatan'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="17" class="px-4 py-6 text-center text-gray-500">Belum ada data laboratorium untuk filter yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($laboratoriumData) > 0)
                    <tfoot class="bg-gray-50">
                        <tr class="font-semibold">
                            <td class="px-3 py-3"></td>
                            <td class="px-4 py-3 text-gray-900">Grand Total</td>
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
                            <td class="px-4 py-3 text-center bg-rose-50">{{ $grandTotals['total_tindakan'] }}</td>
                            <td class="px-4 py-3 text-right bg-rose-50">{{ number_format($grandTotals['total_pendapatan'], 0, ',', '.') }}</td>
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
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    function toggleSyncButton() {
        const anyChecked = document.querySelector('.row-checkbox:checked');
        if (anyChecked) {
            syncButton.classList.remove('hidden');
        } else {
            syncButton.classList.add('hidden');
        }
    }

    selectAllCheckbox.addEventListener('change', function() {
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        toggleSyncButton();
    });

    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = document.querySelectorAll('.row-checkbox:checked').length === rowCheckboxes.length;
            selectAllCheckbox.checked = allChecked;
            toggleSyncButton();
        });
    });

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

        fetch('{{ route("svc-current.laboratorium.sync") }}', {
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
            syncButton.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Sync Selected ke Cost References';
        });
    });
});
</script>
@endsection
