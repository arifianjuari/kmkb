<?php $__env->startSection('title', 'SIMRS Operasi'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Data Operasi dari SIMRS</h1>
            <div class="flex space-x-2">
                <div class="relative rounded-md shadow-sm">
                    <input type="text" id="search-input" class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Cari kode atau nama operasi...">
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

        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Paket Operasi</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Data diambil secara real-time dari database SIMRS.</p>
            </div>
            <div class="border-t border-gray-200">
                <div class="overflow-x-auto">
                <table class="min-w-max w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all-checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Paket</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Perawatan</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Operator 1 (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Asisten Operator 1 (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Asisten Operator 2 (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Asisten Operator 3 (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Instrumen (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Dokter Anak (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Dokter Anestesi (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Asisten Anestesi (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Asisten Anestesi 2 (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Perawat Luar (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Bagian RS (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Omloop (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Omloop 4 (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Omloop 5 (Rp)</th>
                        </tr>
                    </thead>
                    <tbody id="operasi-table" class="bg-white divide-y divide-gray-200">
                        <!-- Data will be loaded here via AJAX -->
                        <tr>
                            <td colspan="18" class="px-6 py-4 text-center text-gray-500">
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
                            Showing <span id="current-page-start">1</span> to <span id="current-page-end">50</span> of <span id="total-records">0</span> results
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

<script>
    // Global variables for pagination
    let currentPage = 0;
    const limit = 50;
    let totalRecords = 0;
    let totalPages = 0;
    let currentSearch = '';
    
    document.addEventListener('DOMContentLoaded', function() {
        // Load operasi data
        loadOperasi();
        
        // Refresh button event
        document.getElementById('refresh-data').addEventListener('click', function() {
            currentPage = 0;
            currentSearch = '';
            document.getElementById('search-input').value = '';
            loadOperasi();
        });
        
        // Search functionality
        document.getElementById('search-button').addEventListener('click', performSearch);
        document.getElementById('search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
        
        // Pagination event listeners for main table
        document.getElementById('prev-page').addEventListener('click', loadPreviousPage);
        document.getElementById('next-page').addEventListener('click', loadNextPage);
        document.getElementById('prev-page-mobile').addEventListener('click', loadPreviousPage);
        document.getElementById('next-page-mobile').addEventListener('click', loadNextPage);
        
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
    
    function loadOperasi() {
        const tableBody = document.getElementById('operasi-table');
        tableBody.innerHTML = '<tr><td colspan="19" class="px-6 py-4 text-center text-gray-500">Loading data...</td></tr>';
        
        // Calculate offset based on current page
        const offset = currentPage * limit;
        
        fetch(`/api/simrs/operasi?limit=${limit}&offset=${offset}&search=${encodeURIComponent(currentSearch)}`, {
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    totalRecords = data.count;
                    totalPages = Math.ceil(totalRecords / limit);
                    
                    // Update pagination info
                    document.getElementById('total-records').textContent = totalRecords;
                    document.getElementById('current-page-start').textContent = offset + 1;
                    document.getElementById('current-page-end').textContent = Math.min(offset + limit, totalRecords);
                    
                    // Update pagination buttons
                    updatePaginationButtons();
                    
                    if (data.data.length > 0) {
                        tableBody.innerHTML = '';
                        data.data.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <input type="checkbox" class="select-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                           data-id="${item.kd_jenis_prw}" 
                                           data-name="${item.nm_perawatan}" 
                                           data-price="${item.total}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.kode_paket}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.nm_perawatan}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.tarif_total)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.operator1)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.asisten_operator1)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.asisten_operator2)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.asisten_operator3)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.instrumen)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.dokter_anak)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.dokter_anestesi)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.asisten_anestesi)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.asisten_anestesi2)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.perawat_luar)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.bagian_rs)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.omloop)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.omloop4)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${formatRupiah(item.omloop5)}</td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="18" class="px-6 py-4 text-center text-gray-500">Tidak ada data tersedia</td></tr>';
                    }
                } else {
                    tableBody.innerHTML = '<tr><td colspan="18" class="px-6 py-4 text-center text-red-500">Error loading data: ' + data.message + '</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tableBody.innerHTML = '<tr><td colspan="18" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
            });
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
    
    // Pagination functions
    function loadPreviousPage() {
        if (currentPage > 0) {
            currentPage--;
            loadOperasi();
        }
    }
    
    function loadNextPage() {
        if (currentPage < totalPages - 1) {
            currentPage++;
            loadOperasi();
        }
    }
    
        
    function updatePaginationButtons() {
        // Update previous buttons
        const prevButtons = [document.getElementById('prev-page'), document.getElementById('prev-page-mobile')];
        prevButtons.forEach(button => {
            button.disabled = currentPage === 0;
            button.classList.toggle('text-gray-400', currentPage === 0);
            button.classList.toggle('text-gray-500', currentPage > 0);
            button.classList.toggle('hover:bg-gray-50', currentPage > 0);
        });
        
        // Update next buttons
        const nextButtons = [document.getElementById('next-page'), document.getElementById('next-page-mobile')];
        nextButtons.forEach(button => {
            button.disabled = currentPage >= totalPages - 1;
            button.classList.toggle('text-gray-400', currentPage >= totalPages - 1);
            button.classList.toggle('text-gray-500', currentPage < totalPages - 1);
            button.classList.toggle('hover:bg-gray-50', currentPage < totalPages - 1);
        });
        
        // Update page number buttons
        updatePageNumberButtons();
    }
    
        
    function updatePageNumberButtons() {
        const container = document.getElementById('page-buttons-container');
        container.innerHTML = '';
        
        if (totalPages <= 7) {
            // Show all pages
            for (let i = 0; i < totalPages; i++) {
                createPageButton(i, container);
            }
        } else {
            // Show first, last, current, and surrounding pages
            if (currentPage > 3) {
                createPageButton(0, container);
                createEllipsis(container);
            }
            
            const start = Math.max(0, Math.min(currentPage - 2, totalPages - 5));
            const end = Math.min(totalPages, Math.max(currentPage + 3, 5));
            
            for (let i = start; i < end; i++) {
                createPageButton(i, container);
            }
            
            if (currentPage < totalPages - 4) {
                createEllipsis(container);
                createPageButton(totalPages - 1, container);
            }
        }
    }
    
        
    function createPageButton(pageNumber, container) {
        const button = document.createElement('button');
        button.className = `relative inline-flex items-center px-4 py-2 text-sm font-semibold ${pageNumber === currentPage ? 'z-10 bg-indigo-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0'} relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus:outline-offset-0`;
        button.textContent = pageNumber + 1;
        button.addEventListener('click', () => {
            currentPage = pageNumber;
            loadOperasi();
        });
        container.appendChild(button);
    }
    
        
    function createEllipsis(container) {
        const span = document.createElement('span');
        span.className = 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0';
        span.textContent = '...';
        container.appendChild(span);
    }
    
    function performSearch() {
        currentSearch = document.getElementById('search-input').value.trim();
        currentPage = 0;
        loadOperasi();
    }
    
    function toggleSyncButton() {
        const anyChecked = document.querySelectorAll('.select-checkbox:checked').length > 0;
        const syncButton = document.getElementById('sync-selected');
        syncButton.classList.toggle('hidden', !anyChecked);
    }
    
    function syncSelectedItems() {
        const selectedItems = [];
        
        // Get selected items from main table
        document.querySelectorAll('.select-checkbox:checked').forEach(checkbox => {
            selectedItems.push({
                kode: checkbox.dataset.id,
                nama: checkbox.dataset.name,
                harga: parseFloat(checkbox.dataset.price) || 0
            });
        });
        
        if (selectedItems.length === 0) {
            alert('Silakan pilih item yang ingin disinkronkan.');
            return;
        }
        
        // Send selected items to backend
        fetch('/api/simrs/sync-operasi', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin',
            body: JSON.stringify({ items: selectedItems })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`${data.message}\n${data.synced_count} item berhasil disinkronkan.`);
                // Refresh data after sync
                loadOperasi();
                // Clear selection
                document.getElementById('select-all-checkbox').checked = false;
                toggleSyncButton();
            } else {
                alert('Error saat menyinkronkan data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saat menyinkronkan data. Silakan coba lagi.');
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/simrs/operasi.blade.php ENDPATH**/ ?>