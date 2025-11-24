@extends('layouts.app')

@section('title', 'SIMRS Master Barang')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Master Barang dari SIMRS</h1>
            <div class="flex space-x-2">
                <div class="relative rounded-md shadow-sm">
                    <input type="text" id="search-input" class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Cari kode atau nama barang...">
                    <div class="absolute inset-y-0 right-0 flex items-center">
                        <button id="search-button" class="h-full rounded-md border-0 bg-transparent py-0 pl-2 pr-3 text-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <button id="refresh-data" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Refresh Data
                </button>
                <button id="sync-selected" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 hidden">
                    Sync Selected
                </button>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Daftar Item Obat/BHP</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Data diambil secara real-time dari database SIMRS.</p>
            </div>
            <div class="border-t border-gray-200">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all-checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga beli dasar (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ralan (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas 3 (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Isi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expire</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Barang</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody id="master-barang-table" class="bg-white divide-y divide-gray-200">
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
            
            <!-- Pagination Controls -->
            <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex flex-1 justify-between sm:hidden">
                    <button id="prev-page-mobile" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</button>
                    <button id="next-page-mobile" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</button>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span id="current-page-start">1</span> to <span id="current-page-end">100</span> of <span id="total-records">0</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <button id="prev-page" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div id="page-buttons-container"></div>
                            <button id="next-page" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
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
    // Global variables for pagination
    let currentPage = 0;
    const limit = 100;
    let totalRecords = 0;
    let totalPages = 0;
    let currentSearch = '';
    
    document.addEventListener('DOMContentLoaded', function() {
        // Load master barang data
        loadMasterBarang();
        
        // Refresh button event
        document.getElementById('refresh-data').addEventListener('click', function() {
            currentPage = 0;
            currentSearch = '';
            document.getElementById('search-input').value = '';
            loadMasterBarang();
        });
        
        // Search functionality
        document.getElementById('search-button').addEventListener('click', performSearch);
        document.getElementById('search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
        
        // Pagination event listeners
        document.getElementById('prev-page').addEventListener('click', loadPreviousPage);
        document.getElementById('next-page').addEventListener('click', loadNextPage);
        document.getElementById('prev-page-mobile').addEventListener('click', loadPreviousPage);
        document.getElementById('next-page-mobile').addEventListener('click', loadNextPage);
        
        // Add event listener for Enter key on page input (if we add one later)
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                // Handle Enter key if needed
            }
        });
        
        // Select all checkbox functionality
        document.getElementById('select-all-checkbox').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.select-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleSyncButton();
        });
        
        // Sync selected button functionality
        document.getElementById('sync-selected').addEventListener('click', syncSelectedItems);
        
        // Add event listener to individual checkboxes
        document.addEventListener('change', function(event) {
            if (event.target.classList.contains('select-checkbox')) {
                toggleSyncButton();
            }
        });
    });
    
    function performSearch() {
        const searchInput = document.getElementById('search-input');
        currentSearch = searchInput.value.trim();
        currentPage = 0; // Reset to first page when searching
        loadMasterBarang();
    }
    
    function loadMasterBarang() {
        const tableBody = document.getElementById('master-barang-table');
        tableBody.innerHTML = '<tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">Loading data...</td></tr>';
        
        let url = `/api/simrs/master-barang?limit=${limit}&offset=${currentPage * limit}`;
        if (currentSearch) {
            url += `&search=${encodeURIComponent(currentSearch)}`;
        }
        
        fetch(url, {
            credentials: 'include',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (response.status === 401) {
                    // Handle unauthenticated response
                    tableBody.innerHTML = '<tr><td colspan="9" class="px-6 py-4 text-center text-red-500">Error: Anda perlu login untuk mengakses data ini.</td></tr>';
                    return Promise.reject('Unauthenticated');
                }
                if (response.status === 400) {
                    // Handle bad request (hospital context not selected for super admin)
                    return response.json().then(data => {
                        tableBody.innerHTML = '<tr><td colspan="9" class="px-6 py-4 text-center text-red-500">Error: ' + data.message + '</td></tr>';
                        return Promise.reject('Hospital context required');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    totalRecords = data.count;
                    totalPages = Math.ceil(totalRecords / limit);
                    updatePaginationInfo();
                    
                    if (data.data.length > 0) {
                        tableBody.innerHTML = '';
                        data.data.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <input type="checkbox" class="select-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                           data-kode="${item.kode_brng}" 
                                           data-nama="${item.nama_brng}" 
                                           data-harga="${item.harga_beli_dasar}"
                                           data-kelas3="${item.kelas3 || 0}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.nama_brng}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.harga_beli_dasar)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${item.ralan ? formatRupiah(item.ralan) : '-'}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${item.kelas3 ? formatRupiah(item.kelas3) : '-'}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${item.isi}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.expire ? formatDate(item.expire) : '-'}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.kode_brng}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.status === '1' ? 'Aktif' : 'Tidak Aktif'}</td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">Tidak ada data tersedia</td></tr>';
                    }
                } else {
                    tableBody.innerHTML = '<tr><td colspan="9" class="px-6 py-4 text-center text-red-500">Error loading data: ' + data.message + '</td></tr>';
                }
            })
            .catch(error => {
                if (error !== 'Unauthenticated' && error !== 'Hospital context required') {
                    console.error('Error:', error);
                    tableBody.innerHTML = '<tr><td colspan="9" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
                }
            });
    }
    
    function loadPreviousPage() {
        if (currentPage > 0) {
            currentPage--;
            loadMasterBarang();
        }
    }
    
    function loadNextPage() {
        // Check if there are more records to load
        if ((currentPage + 1) * limit < totalRecords) {
            currentPage++;
            loadMasterBarang();
        }
    }
    
    function updatePaginationInfo() {
        const start = (currentPage * limit) + 1;
        const end = Math.min((currentPage + 1) * limit, totalRecords);
        
        document.getElementById('current-page-start').textContent = start;
        document.getElementById('current-page-end').textContent = end;
        document.getElementById('total-records').textContent = totalRecords;
        
        // Enable/disable pagination buttons based on current page
        document.getElementById('prev-page').disabled = currentPage === 0;
        document.getElementById('prev-page-mobile').disabled = currentPage === 0;
        document.getElementById('next-page').disabled = (currentPage + 1) * limit >= totalRecords;
        document.getElementById('next-page-mobile').disabled = (currentPage + 1) * limit >= totalRecords;
        
        // Update page number buttons
        updatePageButtons();
    }
    
    function updatePageButtons() {
        const container = document.getElementById('page-buttons-container');
        container.innerHTML = '';
        
        // Show maximum of 5 page buttons
        let startPage = Math.max(0, currentPage - 2);
        let endPage = Math.min(totalPages - 1, startPage + 4);
        
        // Adjust startPage if we're near the end
        if (endPage - startPage < 4) {
            startPage = Math.max(0, endPage - 4);
        }
        
        // Create page buttons
        for (let i = startPage; i <= endPage; i++) {
            const button = document.createElement('button');
            button.className = 'relative inline-flex items-center px-4 py-2 text-sm font-semibold ' + 
                              (i === currentPage ? 
                               'z-10 bg-indigo-600 text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' : 
                               'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0');
            button.textContent = i + 1;
            button.addEventListener('click', () => {
                currentPage = i;
                loadMasterBarang();
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
                currentPage = totalPages - 1;
                loadMasterBarang();
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
        return rupiah;
    }
    
    function formatDate(dateString) {
        if (!dateString) return '-';
        
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        
        return `${day}-${month}-${year}`;
    }
    
    function toggleSyncButton() {
        const selectedCheckboxes = document.querySelectorAll('.select-checkbox:checked');
        const syncButton = document.getElementById('sync-selected');
        
        if (selectedCheckboxes.length > 0) {
            syncButton.classList.remove('hidden');
        } else {
            syncButton.classList.add('hidden');
        }
    }
    
    function syncSelectedItems() {
        const selectedCheckboxes = document.querySelectorAll('.select-checkbox:checked');
        
        if (selectedCheckboxes.length === 0) {
            alert('Silakan pilih item yang ingin disinkronkan.');
            return;
        }
        
        // Prepare data for syncing
        const itemsToSync = [];
        selectedCheckboxes.forEach(checkbox => {
            itemsToSync.push({
                kode_brng: checkbox.dataset.kode,
                nama_brng: checkbox.dataset.nama,
                harga_beli_dasar: checkbox.dataset.harga,
                kelas3: checkbox.dataset.kelas3
            });
        });
        
        // Send data to server
        syncButton = document.getElementById('sync-selected');
        syncButton.disabled = true;
        syncButton.textContent = 'Syncing...';
        
        fetch('/api/simrs/sync-master-barang', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ items: itemsToSync })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`${data.synced_count} item berhasil disinkronkan ke Cost References.`);
                // Reset selection
                document.getElementById('select-all-checkbox').checked = false;
                selectedCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                toggleSyncButton();
                // Reload data to reflect changes
                loadMasterBarang();
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
