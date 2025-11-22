<?php $__env->startSection('title', 'SIMRS Tindakan Rawat Inap'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Tindakan Rawat Inap dari SIMRS</h1>
            <div class="flex items-center space-x-2">
                <div class="relative">
                    <input type="text" id="search-tindakan-inap" placeholder="Cari kode atau nama tindakan..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pl-10">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <button id="sync-selected" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mr-2 hidden">
                    <svg id="sync-spinner" class="-ml-1 mr-2 h-5 w-5 animate-spin hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span id="sync-text">Sync Selected</span>
                </button>
                <button id="refresh-data" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Refresh Data
                </button>
                <span id="sync-status" class="ml-2 text-sm hidden"></span>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Tindakan Rawat Inap</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Data diambil secara real-time dari database SIMRS.</p>
            </div>
            <div class="border-t border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all-inap" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Tindakan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                        </tr>
                    </thead>
                    <tbody id="tindakan-rawat-inap-table" class="bg-white divide-y divide-gray-200">
                        <!-- Data will be loaded here via AJAX -->
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Loading data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Controls for Tindakan Rawat Inap -->
            <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex flex-1 justify-between sm:hidden">
                    <button id="prev-inap-mobile" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</button>
                    <button id="next-inap-mobile" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</button>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span id="inap-current-page-start">1</span> to <span id="inap-current-page-end">30</span> of <span id="inap-total-records">0</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <button id="prev-inap" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div id="inap-page-buttons-container"></div>
                            <button id="next-inap" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
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
    let currentPage = 0;
    let totalRecords = 0;
    const limit = 30;
    let searchTindakanInap = '';

    document.addEventListener('DOMContentLoaded', function() {
        // Load tindakan data
        loadTindakanRawatInap();
        
        // Refresh button event
        document.getElementById('refresh-data').addEventListener('click', function() {
            currentPage = 0;
            loadTindakanRawatInap();
        });
        
        // Search input event
        const searchInput = document.getElementById('search-tindakan-inap');
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchTindakanInap = this.value;
                currentPage = 0; // Reset to first page on search
                loadTindakanRawatInap();
            }, 500); // Debounce for 500ms
        });
        
        // Pagination event listeners for Tindakan Rawat Inap
        document.getElementById('prev-inap').addEventListener('click', loadPreviousInapPage);
        document.getElementById('next-inap').addEventListener('click', loadNextInapPage);
        document.getElementById('prev-inap-mobile').addEventListener('click', loadPreviousInapPage);
        document.getElementById('next-inap-mobile').addEventListener('click', loadNextInapPage);
        
        // Select all checkboxes event listeners
        document.getElementById('select-all-inap').addEventListener('change', function(e) {
            const checkboxes = document.querySelectorAll('#tindakan-rawat-inap-table .row-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = e.target.checked;
            });
            updateSyncButton();
        });
        
        // Row checkbox event delegation
        document.getElementById('tindakan-rawat-inap-table').addEventListener('change', function(e) {
            if (e.target.classList.contains('row-checkbox')) {
                updateSyncButton();
            }
        });
        
        // Sync button event listener
        document.getElementById('sync-selected').addEventListener('click', syncSelectedItems);
    });
    
    function loadTindakanRawatInap() {
        const tableBody = document.getElementById('tindakan-rawat-inap-table');
        const syncButton = document.getElementById('sync-selected');
        const selectAllCheckbox = document.getElementById('select-all-inap');
        
        tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Loading data...</td></tr>';
        syncButton.classList.add('hidden');
        selectAllCheckbox.checked = false;
        
        // Build URL with pagination and search parameters
        let url = `/api/simrs/tindakan-rawat-inap?limit=${limit}&offset=${currentPage * limit}`;
        
        if (searchTindakanInap) {
            url += `&search=${encodeURIComponent(searchTindakanInap)}`;
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
                    totalRecords = data.total;
                    updateInapPaginationInfo();
                    
                    if (data.data.length > 0) {
                        tableBody.innerHTML = '';
                        data.data.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <input type="checkbox" class="row-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                        data-kode="${item.kd_jenis_prw}" 
                                        data-nama="${item.nm_perawatan}" 
                                        data-harga="${item.total_byrdrpr || 0}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.kd_jenis_prw}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.nm_perawatan}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatRupiah(item.total_byrdrpr || 0)}</td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data tersedia</td></tr>';
                    }
                } else if (data && data.message && data.message.includes('Super admin must select a hospital context')) {
                    // Redirect to hospital selection page
                    window.location.href = '/hospitals/select';
                } else {
                    tableBody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-red-500">Error loading data: ' + (data ? data.message : 'Unknown error') + '</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tableBody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
            });
    }
    
    function loadPreviousInapPage() {
        if (currentPage > 0) {
            currentPage--;
            loadTindakanRawatInap();
        }
    }
    
    function loadNextInapPage() {
        // Check if there are more records to load
        if ((currentPage + 1) * limit < totalRecords) {
            currentPage++;
            loadTindakanRawatInap();
        }
    }
    
    function updateInapPaginationInfo() {
        const start = (currentPage * limit) + 1;
        const end = Math.min((currentPage + 1) * limit, totalRecords);
        
        document.getElementById('inap-current-page-start').textContent = start;
        document.getElementById('inap-current-page-end').textContent = end;
        document.getElementById('inap-total-records').textContent = totalRecords;
        
        // Enable/disable pagination buttons based on current page
        document.getElementById('prev-inap').disabled = currentPage === 0;
        document.getElementById('prev-inap-mobile').disabled = currentPage === 0;
        document.getElementById('next-inap').disabled = (currentPage + 1) * limit >= totalRecords;
        document.getElementById('next-inap-mobile').disabled = (currentPage + 1) * limit >= totalRecords;
        
        // Update page number buttons
        updateInapPageButtons();
    }
    
    function updateInapPageButtons() {
        const container = document.getElementById('inap-page-buttons-container');
        if (!container) return;
        
        container.innerHTML = '';
        
        const totalPages = Math.ceil(totalRecords / limit);
        
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
                loadTindakanRawatInap();
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
                loadTindakanRawatInap();
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
        const checkboxes = document.querySelectorAll('#tindakan-rawat-inap-table .row-checkbox:checked');
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
        const checkboxes = document.querySelectorAll('#tindakan-rawat-inap-table .row-checkbox:checked');
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
        const statusEl = document.getElementById('sync-status');
        
        syncButton.disabled = true;
        syncText.textContent = 'Syncing...';
        syncSpinner.classList.remove('hidden');
        
        // Send to server
        fetch('/api/simrs/sync-tindakan-rawat-inap', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ items: items }),
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success alert
                const alertDiv = document.createElement('div');
                alertDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
                alertDiv.textContent = `${data.synced_count} item${data.synced_count !== 1 ? 's' : ''} berhasil disinkronkan ke referensi biaya`;
                document.body.appendChild(alertDiv);

                setTimeout(() => {
                    document.body.removeChild(alertDiv);
                }, 3000);

                // Inline status indicator
                statusEl.textContent = `${data.synced_count} item berhasil disinkronkan`;
                statusEl.classList.remove('hidden', 'text-red-600');
                statusEl.classList.add('text-green-600');

                // Highlight selected rows briefly
                const selectedRows = Array.from(document.querySelectorAll('#tindakan-rawat-inap-table .row-checkbox:checked'))
                  .map(cb => cb.closest('tr'));
                selectedRows.forEach(tr => tr.classList.add('bg-green-50'));

                // Reset checkboxes and reload data
                document.getElementById('select-all-inap').checked = false;
                // Delay reload to let user see highlights
                setTimeout(() => {
                    // Clear highlights and inline status
                    selectedRows.forEach(tr => tr && tr.classList && tr.classList.remove('bg-green-50'));
                    statusEl.classList.add('hidden');
                    loadTindakanRawatInap();
                }, 1200);
            } else {
                // Show error alert
                const alertDiv = document.createElement('div');
                alertDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50';
                alertDiv.textContent = 'Error menyinkronkan item: ' + data.message;
                document.body.appendChild(alertDiv);

                setTimeout(() => {
                    document.body.removeChild(alertDiv);
                }, 3000);

                // Inline status indicator error
                statusEl.textContent = 'Gagal menyinkronkan: ' + (data.message || 'Terjadi kesalahan');
                statusEl.classList.remove('hidden', 'text-green-600');
                statusEl.classList.add('text-red-600');
                setTimeout(() => statusEl.classList.add('hidden'), 4000);
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

            // Inline status indicator error
            statusEl.textContent = 'Error jaringan/server saat sync';
            statusEl.classList.remove('hidden', 'text-green-600');
            statusEl.classList.add('text-red-600');
            setTimeout(() => statusEl.classList.add('hidden'), 4000);
        })
        .finally(() => {
            // Reset sync button state
            syncButton.disabled = false;
            syncText.textContent = 'Sync Selected';
            syncSpinner.classList.add('hidden');
        });
    }
    
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/simrs/tindakan-rawat-inap.blade.php ENDPATH**/ ?>