@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Cost Catalogue') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('cost-references-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is Cost Reference?') }}"
                    title="{{ __('What is Cost Catalogue?') }}"
                >
                    i
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <form method="GET" action="{{ route('cost-references.index') }}" class="flex items-center space-x-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('Search...') }}" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                    </div>
                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                        {{ __('Search') }}
                    </button>
                    @if($search || request('category'))
                        <a href="{{ route('cost-references.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </form>
                <a href="{{ route('cost-references.template') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    {{ __('Download Template') }}
                </a>
                <button type="button" onclick="openImportModal()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('Import Excel') }}
                </button>
                <a href="{{ route('cost-references.export', ['search' => $search ?? '', 'category' => $category ?? '']) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ __('Export Excel') }}
                </a>
                <a href="{{ route('cost-references.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    {{ __('Add New Cost Catalogue Item') }}
                </a>
            </div>
        </div>
        <div id="cost-references-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Cost Catalogue</span> adalah "buku besar" daftar layanan & item di rumah sakit yang bisa muncul di pathway, unit cost, dan tarif.
            </p>
            <div class="mb-2">
                <p class="font-semibold mb-1">Isi utama:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Kode & nama layanan/item (tindakan, lab, obat, BMHP, paket, dll)</li>
                    <li>Jenis (service vs barang/material)</li>
                    <li>Cost center utama yang bertanggung jawab</li>
                    <li>Satuan (per kali, per hari, per botol, dst)</li>
                    <li>(Opsional) mapping ke pathway / kategori klinis</li>
                </ul>
            </div>
            <div>
                <p class="font-semibold mb-1">Peran di sistem:</p>
                <p class="ml-2">Menjadi kunci (ID) yang menghubungkan:</p>
                <ul class="list-disc list-inside space-y-1 ml-4">
                    <li>Perhitungan unit cost</li>
                    <li>Clinical pathway</li>
                    <li>Tarif (termasuk mapping ke INA-CBG)</li>
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
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('import_errors') && count(session('import_errors')) > 0)
            <div class="mb-6 rounded-md bg-yellow-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            {{ __('Import Errors') }}
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @php
                    $categoryTabs = [
                        'all' => __('All Categories'),
                        'barang' => __('Barang/BMHP'),
                        'tindakan_rj' => __('Tindakan Rawat Jalan'),
                        'tindakan_ri' => __('Tindakan Rawat Inap'),
                        'laboratorium' => __('Laboratorium'),
                        'radiologi' => __('Radiologi'),
                        'operasi' => __('Operasi'),
                        'kamar' => __('Kamar'),
                    ];
                @endphp

                {{-- Category Tabs (Indigo) --}}
                <div class="mb-6">
                    <p class="text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">{{ __('Category') }}</p>
                    <div class="flex flex-wrap items-center gap-2.5">
                        @foreach($categoryTabs as $key => $label)
                            @php
                                $isActiveTab = ($key === 'all' && !$category) || ($key === $category);
                                $urlParams = request()->except('category', 'page');
                                if ($key !== 'all') {
                                    $urlParams['category'] = $key;
                                }
                                $tabUrl = route('cost-references.index', $urlParams);
                            @endphp
                            <a
                                href="{{ $tabUrl }}"
                                class="inline-flex items-center gap-2 px-2.5 py-1 border rounded-full text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-biru-dongker-700 {{ $isActiveTab ? 'bg-biru-dongker-800 text-white border-biru-dongker-800' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}"
                            >
                                <span>{{ $label }}</span>
                                @if(isset($categoryCounts))
                                    <span class="ml-1 text-[10px] font-semibold {{ $isActiveTab ? 'text-white/80' : 'text-gray-500' }}">
                                        {{ $categoryCounts[$key] ?? 0 }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                @if($costReferences->count() > 0)
                    <form id="bulk-delete-form" action="{{ route('cost-references.bulk-destroy') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm text-gray-600">
                                {{ __('Select records to delete in bulk') }}
                            </div>
                            <button id="bulk-delete-btn" type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed" disabled onclick="return confirm('{{ __('Are you sure you want to delete the selected cost catalogue items? This action cannot be undone.') }}')">
                                {{ __('Delete Selected') }}
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <input id="select-all" type="checkbox" class="h-4 w-4 text-biru-dongker-800 border-gray-300 rounded">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Description') }}</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Standard Cost (Rp)') }}</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Purchase Price (Rp)') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Unit') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service Code') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Source') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Updated At') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($costReferences as $reference)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <input type="checkbox" name="ids[]" value="{{ $reference->id }}" class="row-checkbox h-4 w-4 text-biru-dongker-800 border-gray-300 rounded">
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reference->service_description }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($reference->standard_cost, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($reference->purchase_price, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reference->unit }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reference->service_code }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reference->source }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reference->updated_at ? $reference->updated_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('cost-references.show', $reference) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('View') }}" aria-label="{{ __('View') }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                            <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('cost-references.edit', $reference) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-biru-dongker-800 hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('cost-references.destroy', $reference) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this cost catalogue item?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                                <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1Zm-3 6h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 9Zm3 2v8h2v-8H9Zm4 0v8h2v-8h-2Z"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                    
                    <div class="mt-6">
                        {{ $costReferences->links() }}
                    </div>
                @else
                    <p class="text-gray-600">{{ __('No cost catalogue items found.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Import Cost Catalogue') }}</h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('cost-references.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="mb-4">
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('Pilih File Excel') }}
                    </label>
                    <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-biru-dongker-200 file:text-biru-dongker-900 hover:file:bg-biru-dongker-300">
                    <p class="mt-1 text-xs text-gray-500">
                        {{ __('Format yang didukung: .xlsx, .xls, .csv (Maks: 10MB)') }}
                    </p>
                </div>
                
                <div class="mb-4 p-3 bg-blue-50 rounded-md">
                    <p class="text-sm text-blue-800">
                        <strong>{{ __('Format File:') }}</strong><br>
                        {{ __('Kolom: Service Code, Service Description, Standard Cost, Unit, Source, Category (opsional)') }}
                    </p>
                    <p class="text-xs text-blue-600 mt-1">
                        {{ __('Category valid: barang, tindakan_rj, tindakan_ri, laboratorium, radiologi, operasi, kamar') }}
                    </p>
                    <a href="{{ route('cost-references.template') }}" class="text-sm text-blue-600 hover:text-blue-800 underline mt-2 inline-block">
                        {{ __('Download Template Excel') }}
                    </a>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeImportModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                        {{ __('Batal') }}
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                        {{ __('Import') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const bulkBtn = document.getElementById('bulk-delete-btn');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkForm = document.getElementById('bulk-delete-form');
        
        // Update button state based on checkbox selection
        function updateButtonState() {
            const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
            bulkBtn.disabled = !anyChecked;
        }
        
        // Select all functionality
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                rowCheckboxes.forEach(cb => { cb.checked = selectAll.checked; });
                updateButtonState();
            });
        }
        
        // Individual checkbox change
        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                if (selectAll) selectAll.checked = allChecked;
                updateButtonState();
            });
        });
        
        // Form submission with confirmation
        if (bulkForm) {
            bulkForm.addEventListener('submit', function(e) {
                const selectedCount = Array.from(rowCheckboxes).filter(cb => cb.checked).length;
                if (selectedCount === 0) {
                    e.preventDefault();
                    alert('Please select at least one item to delete.');
                    return false;
                }
                
                if (!confirm('Are you sure you want to delete the selected cost catalogue items? This action cannot be undone.')) {
                    e.preventDefault();
                    return false;
                }
            });
        }
        
        // Initialize state
        updateButtonState();
    });

    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
        document.getElementById('importForm').reset();
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('importModal');
        if (event.target == modal) {
            closeImportModal();
        }
    }
</script>
@endpush
