@extends('layouts.app')

@section('title', 'SIMRS Kamar')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Data Kamar dari SIMRS</h1>
            <div class="flex items-center space-x-2">
                <div class="relative">
                    <input type="text" id="search-kamar" placeholder="Cari kode kamar, bangsal, atau kelas..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm pl-10">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <button id="refresh-data" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    Refresh Data
                </button>
                <div id="selection-actions" class="hidden ml-2 flex items-center space-x-2">
                    <span id="selected-count" class="text-sm text-gray-600">0 dipilih</span>
                    <button id="sync-selected" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        Sync Selected
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Data Kamar</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Data diambil secara real-time dari database SIMRS.</p>
            </div>
            <div class="border-t border-gray-200">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3">
                                <input id="select-all" type="checkbox" class="h-4 w-4 text-biru-dongker-800 border-gray-300 rounded">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Kamar</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Bangsal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarif Kamar</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        </tr>
                    </thead>
                    <tbody id="kamar-table" class="bg-white divide-y divide-gray-200">
                        <!-- Data will be loaded here via AJAX -->
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Loading data...
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
            <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button id="prev-page-mobile" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </button>
                    <button id="next-page-mobile" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span id="current-page-start">0</span> to <span id="current-page-end">0</span> of <span id="total-records">0</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <button id="prev-page" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button id="next-page" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPage = 0;
    const limit = 50;
    let selectedMap = new Map();
    
    document.addEventListener('DOMContentLoaded', function() {
        loadKamar();
        
        // Search functionality
        const searchInput = document.getElementById('search-kamar');
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 0;
                loadKamar();
            }, 500);
        });
        
        // Refresh button
        document.getElementById('refresh-data').addEventListener('click', function() {
            currentPage = 0;
            loadKamar();
        });
        
        // Pagination
        document.getElementById('prev-page').addEventListener('click', function() {
            if (currentPage > 0) {
                currentPage--;
                loadKamar();
            }
        });
        
        document.getElementById('next-page').addEventListener('click', function() {
            currentPage++;
            loadKamar();
        });
        
        document.getElementById('prev-page-mobile').addEventListener('click', function() {
            if (currentPage > 0) {
                currentPage--;
                loadKamar();
            }
        });
        
        document.getElementById('next-page-mobile').addEventListener('click', function() {
            currentPage++;
            loadKamar();
        });

        // Sync selected
        document.getElementById('sync-selected').addEventListener('click', syncSelectedKamar);
        // Select all
        document.getElementById('select-all').addEventListener('change', (e) => toggleSelectAll(e.target.checked));
    });
    
    function loadKamar() {
        const search = document.getElementById('search-kamar').value;
        const offset = currentPage * limit;
        
        // Show loading state
        const tableBody = document.getElementById('kamar-table');
        tableBody.innerHTML = `<tr>
            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                Loading data...
            </td>
        </tr>`;
        
        // Fetch data from API
        fetch(`/api/simrs/kamar?limit=${limit}&offset=${offset}&search=${encodeURIComponent(search)}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }
                if (response.status === 403) {
                    window.location.href = '/hospital-selection';
                    return;
                }
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                renderKamarTable(data.data, data.total);
                updatePagination(data.total);
            } else {
                tableBody.innerHTML = `<tr>
                    <td colspan="5" class="px-6 py-4 text-center text-red-500">
                        Error loading data: ${data.message}
                    </td>
                </tr>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tableBody.innerHTML = `<tr>
                <td colspan="5" class="px-6 py-4 text-center text-red-500">
                    Error loading data. Please try again.
                </td>
            </tr>`;
        });
    }
    
    function renderKamarTable(data, total) {
        const tableBody = document.getElementById('kamar-table');
        
        if (data.length === 0) {
            tableBody.innerHTML = `<tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                    No data found
                </td>
            </tr>`;
            return;
        }

        // Reset selection for current page
        selectedMap.clear();
        updateSelectionUI();

        let rows = '';
        data.forEach(item => {
            const key = item.kd_kamar;
            rows += `
                <tr>
                    <td class="px-4 py-4">
                        <input type="checkbox" class="row-checkbox h-4 w-4 text-biru-dongker-800 border-gray-300 rounded" data-kode="${item.kd_kamar}" data-nama="${item.kd_bangsal} - ${item.kelas}" data-harga="${item.trf_kamar}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.kd_kamar}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.kd_bangsal}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatRupiah(item.trf_kamar)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.kelas}</td>
                </tr>
            `;
        });
        
        tableBody.innerHTML = rows;

        // Bind row checkbox events
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.addEventListener('change', (e) => {
                const el = e.target;
                const kode = el.getAttribute('data-kode');
                const nama = el.getAttribute('data-nama');
                const harga = Number(el.getAttribute('data-harga')) || 0;
                if (el.checked) {
                    selectedMap.set(kode, { kode, nama, harga });
                } else {
                    selectedMap.delete(kode);
                }
                updateSelectionUI();
            });
        });
    }
    
    function updatePagination(total) {
        const start = (currentPage * limit) + 1;
        const end = Math.min((currentPage + 1) * limit, total);
        
        document.getElementById('current-page-start').textContent = start;
        document.getElementById('current-page-end').textContent = end;
        document.getElementById('total-records').textContent = total;
        
        // Disable/enable pagination buttons
        document.getElementById('prev-page').disabled = currentPage === 0;
        document.getElementById('prev-page-mobile').disabled = currentPage === 0;
        document.getElementById('next-page').disabled = end >= total;
        document.getElementById('next-page-mobile').disabled = end >= total;
    }
    
    function formatRupiah(angka) {
        let number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return 'Rp ' + rupiah;
    }

    function updateSelectionUI() {
        const count = selectedMap.size;
        const actions = document.getElementById('selection-actions');
        const countEl = document.getElementById('selected-count');
        if (count > 0) {
            actions.classList.remove('hidden');
        } else {
            actions.classList.add('hidden');
        }
        countEl.textContent = `${count} dipilih`;
        // Uncheck select-all if nothing selected
        const selectAll = document.getElementById('select-all');
        if (selectAll) selectAll.checked = false;
    }

    function toggleSelectAll(checked) {
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.checked = checked;
            const kode = cb.getAttribute('data-kode');
            const nama = cb.getAttribute('data-nama');
            const harga = Number(cb.getAttribute('data-harga')) || 0;
            if (checked) {
                selectedMap.set(kode, { kode, nama, harga });
            } else {
                selectedMap.delete(kode);
            }
        });
        updateSelectionUI();
    }

    function syncSelectedKamar() {
        const items = Array.from(selectedMap.values());
        if (items.length === 0) return;

        fetch('/api/simrs/sync-kamar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin',
            body: JSON.stringify({ items })
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }
                if (response.status === 403) {
                    window.location.href = '/hospital-selection';
                    return;
                }
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                alert(`${data.synced_count} item berhasil di-sync.`);
                // Clear selection after sync
                selectedMap.clear();
                updateSelectionUI();
            } else {
                alert(`Gagal sync: ${data && data.message ? data.message : 'Unknown error'}`);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan saat sync.');
        });
    }
</script>
@endsection
