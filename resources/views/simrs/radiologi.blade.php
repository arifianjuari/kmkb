@extends('layouts.app')

@section('title', 'SIMRS Radiologi')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Data Radiologi dari SIMRS</h1>
            <div class="flex items-center space-x-2">
                <div class="relative">
                    <input type="text" id="search-radiologi" placeholder="Cari kode atau nama pemeriksaan..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pl-10">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <button id="sync-selected" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mr-2 hidden">
                    <span id="sync-text">Sync Selected</span>
                    <svg id="sync-spinner" class="hidden ml-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
                <button id="refresh-data" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Refresh Data
                </button>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Daftar Pemeriksaan Radiologi</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Data diambil secara real-time dari database SIMRS.</p>
            </div>
            <div class="border-t border-gray-200">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all-radiologi" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pemeriksaan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BHP</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokter</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KSO</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manajemen</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        </tr>
                    </thead>
                    <tbody id="radiologi-table" class="bg-white divide-y divide-gray-200">
                        <!-- Data will be loaded here via AJAX -->
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                Loading data...
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
            
            <!-- Pagination Controls for Radiologi -->
            <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex flex-1 justify-between sm:hidden">
                    <button id="prev-radiologi-mobile" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</button>
                    <button id="next-radiologi-mobile" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</button>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span id="radiologi-current-page-start">1</span> to <span id="radiologi-current-page-end">100</span> of <span id="radiologi-total-records">0</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <button id="prev-radiologi" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div id="radiologi-page-buttons-container"></div>
                            <button id="next-radiologi" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
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
    // Global variables for pagination and search
    let radiologiCurrentPage = 0;
    const limit = 30;
    let radiologiTotalRecords = 0;
    let searchRadiologi = '';
    
    document.addEventListener('DOMContentLoaded', function() {
        // Load radiologi data
        loadRadiologi();
        
        // Refresh button event
        document.getElementById('refresh-data').addEventListener('click', function() {
            radiologiCurrentPage = 0;
            loadRadiologi();
        });
        
        // Search input event
        const searchInput = document.getElementById('search-radiologi');
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchRadiologi = this.value;
                radiologiCurrentPage = 0; // Reset to first page on search
                loadRadiologi();
            }, 500); // Debounce for 500ms
        });
        
        // Pagination event listeners for Radiologi
        document.getElementById('prev-radiologi').addEventListener('click', loadPreviousRadiologiPage);
        document.getElementById('next-radiologi').addEventListener('click', loadNextRadiologiPage);
        document.getElementById('prev-radiologi-mobile').addEventListener('click', loadPreviousRadiologiPage);
        document.getElementById('next-radiologi-mobile').addEventListener('click', loadNextRadiologiPage);
        
        // Select all checkboxes event listeners
        document.getElementById('select-all-radiologi').addEventListener('change', function(e) {
            const checkboxes = document.querySelectorAll('#radiologi-table .row-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = e.target.checked;
            });
            updateSyncButton();
        });
        
        // Row checkbox event delegation
        document.getElementById('radiologi-table').addEventListener('change', function(e) {
            if (e.target.classList.contains('row-checkbox')) {
                updateSyncButton();
            }
        });
        
        // Sync button event listener
        document.getElementById('sync-selected').addEventListener('click', syncSelectedItems);
    });
    
    function loadRadiologi() {
        const tableBody = document.getElementById('radiologi-table');
        const syncButton = document.getElementById('sync-selected');
        const selectAllCheckbox = document.getElementById('select-all-radiologi');
        
        tableBody.innerHTML = '<tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">Loading data...</td></tr>';
        syncButton.classList.add('hidden');
        selectAllCheckbox.checked = false;
        
        // Build URL with pagination and search parameters
        let url = `/api/simrs/radiologi?limit=${limit}&offset=${radiologiCurrentPage * limit}`;
        
        if (searchRadiologi) {
            url += `&search=${encodeURIComponent(searchRadiologi)}`;
        }
        
        fetch(url)
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    // If not JSON, it might be a redirect to login or hospital selection
                    window.location.reload();
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    radiologiTotalRecords = data.total;
                    updateRadiologiPaginationInfo();
                    
                    if (data.data.length > 0) {
                        tableBody.innerHTML = '';
                        data.data.forEach(item => {
                            const dokter = Number(item.tarif_tindakan_dokter || 0);
                            const petugas = Number(item.tarif_tindakan_petugas || 0);
                            const kso = Number(item.kso || 0);
                            const menejemen = Number(item.menejemen || 0);
                            const totalByr = (item.total_byr !== null && item.total_byr !== undefined)
                                ? Number(item.total_byr)
                                : (dokter + petugas + kso + menejemen);

                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <input type="checkbox" class="row-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                        data-kode="${item.kd_jenis_prw}" 
                                        data-nama="${item.nm_perawatan}" 
                                        data-harga="${totalByr}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.nm_perawatan}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatRupiah(item.bhp || 0)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatRupiah(dokter)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatRupiah(petugas)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatRupiah(kso)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatRupiah(menejemen)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatRupiah(totalByr)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.kd_jenis_prw}</td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">Tidak ada data tersedia</td></tr>';
                    }
                } else if (data && data.message && data.message.includes('Super admin must select a hospital context')) {
                    // Redirect to hospital selection page
                    window.location.href = '/hospitals/select';
                } else {
                    tableBody.innerHTML = '<tr><td colspan="9" class="px-6 py-4 text-center text-red-500">Error loading data: ' + (data ? data.message : 'Unknown error') + '</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tableBody.innerHTML = '<tr><td colspan="9" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
            });
    }
    
    
    function loadPreviousRadiologiPage() {
        if (radiologiCurrentPage > 0) {
            radiologiCurrentPage--;
            loadRadiologi();
        }
    }
    
    function loadNextRadiologiPage() {
        // Check if there are more records to load
        if ((radiologiCurrentPage + 1) * limit < radiologiTotalRecords) {
            radiologiCurrentPage++;
            loadRadiologi();
        }
    }
    
    function updateRadiologiPaginationInfo() {
        const start = (radiologiCurrentPage * limit) + 1;
        const end = Math.min((radiologiCurrentPage + 1) * limit, radiologiTotalRecords);
        
        document.getElementById('radiologi-current-page-start').textContent = start;
        document.getElementById('radiologi-current-page-end').textContent = end;
        document.getElementById('radiologi-total-records').textContent = radiologiTotalRecords;
        
        // Enable/disable pagination buttons based on current page
        document.getElementById('prev-radiologi').disabled = radiologiCurrentPage === 0;
        document.getElementById('prev-radiologi-mobile').disabled = radiologiCurrentPage === 0;
        document.getElementById('next-radiologi').disabled = (radiologiCurrentPage + 1) * limit >= radiologiTotalRecords;
        document.getElementById('next-radiologi-mobile').disabled = (radiologiCurrentPage + 1) * limit >= radiologiTotalRecords;
        
        // Update page number buttons
        updateRadiologiPageButtons();
    }
    
    function updateRadiologiPageButtons() {
        const container = document.getElementById('radiologi-page-buttons-container');
        if (!container) return;
        
        container.innerHTML = '';
        
        const totalPages = Math.ceil(radiologiTotalRecords / limit);
        
        // Show maximum of 5 page buttons
        let startPage = Math.max(0, radiologiCurrentPage - 2);
        let endPage = Math.min(totalPages - 1, startPage + 4);
        
        // Adjust startPage if we're near the end
        if (endPage - startPage < 4) {
        startPage = Math.max(0, endPage - 4);
        }
        
        // Create page buttons
        for (let i = startPage; i <= endPage; i++) {
            const button = document.createElement('button');
            button.className = 'relative inline-flex items-center px-4 py-2 text-sm font-semibold ' + 
                              (i === radiologiCurrentPage ? 
                               'z-10 bg-indigo-600 text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' : 
                               'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0');
            button.textContent = i + 1;
            button.addEventListener('click', () => {
                radiologiCurrentPage = i;
                loadRadiologi();
            });
            container.appendChild(button);
        }
        
        // Add ellipsis if there are more pages
        if (endPage < totalPages - 1) {
            const ellipsis = document.createElement('span');
            ellipsis.className = 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0';
            ellipsis.textContent = '...';
            container.appendChild(ellipsis);
            
            // Add last page button
            const lastPageButton = document.createElement('button');
            lastPageButton.className = 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0';
            lastPageButton.textContent = totalPages;
            lastPageButton.addEventListener('click', () => {
                radiologiCurrentPage = totalPages - 1;
                loadRadiologi();
            });
            container.appendChild(lastPageButton);
        }
    }
    
    function formatRupiah(angka) {
        var number_string = angka.toString().replace(/[^,\d]/g, ''),
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
    
    function updateSyncButton() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        const syncButton = document.getElementById('sync-selected');
        
        if (checkboxes.length > 0) {
            syncButton.classList.remove('hidden');
            syncButton.disabled = false;
        } else {
            syncButton.classList.add('hidden');
            syncButton.disabled = true;
        }
    }
    
    function syncSelectedItems() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        const items = [];
        
        checkboxes.forEach(checkbox => {
            items.push({
                kode: checkbox.dataset.kode,
                nama: checkbox.dataset.nama,
                harga: parseFloat(checkbox.dataset.harga)
            });
        });
        
        if (items.length === 0) return;
        
        // Update UI to show syncing state
        const syncButton = document.getElementById('sync-selected');
        const syncText = document.getElementById('sync-text');
        const syncSpinner = document.getElementById('sync-spinner');
        
        syncButton.disabled = true;
        syncText.textContent = 'Syncing...';
        syncSpinner.classList.remove('hidden');
        
        // Send to server
        fetch('/api/simrs/sync-radiologi', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ items: items }),
            credentials: 'same-origin'
        })
        .then(response => {
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // If not JSON, it might be a redirect to login or hospital selection
                window.location.reload();
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                // Show success alert
                const alertDiv = document.createElement('div');
                alertDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
                alertDiv.textContent = `${data.synced_count} item${data.synced_count !== 1 ? 's' : ''} berhasil disinkronkan ke referensi biaya`;
                document.body.appendChild(alertDiv);
                
                setTimeout(() => {
                    document.body.removeChild(alertDiv);
                }, 3000);
                
                // Reset checkboxes and reload data
                document.getElementById('select-all-radiologi').checked = false;
                loadRadiologi();
            } else if (data && data.message && data.message.includes('Super admin must select a hospital context')) {
                // Redirect to hospital selection page
                window.location.href = '/hospitals/select';
            } else {
                // Show error alert
                const alertDiv = document.createElement('div');
                alertDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50';
                alertDiv.textContent = 'Error menyinkronkan item: ' + (data ? data.message : 'Unknown error');
                document.body.appendChild(alertDiv);
                
                setTimeout(() => {
                    document.body.removeChild(alertDiv);
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Show error alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50';
            alertDiv.textContent = 'Error menyinkronkan item';
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                document.body.removeChild(alertDiv);
            }, 3000);
        })
        .finally(() => {
            // Reset sync button state
            syncButton.disabled = false;
            syncText.textContent = 'Sync Selected';
            syncSpinner.classList.add('hidden');
        });
    }
</script>
@endsection
