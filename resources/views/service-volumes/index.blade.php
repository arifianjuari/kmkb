@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Service Volumes') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('service-volumes-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is Service Volume?') }}"
                    title="{{ __('What is Service Volume?') }}"
                >
                    i
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <form method="GET" action="{{ route('service-volumes.index') }}" class="flex items-center space-x-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('Search...') }}" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                    </div>
                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                        {{ __('Search') }}
                    </button>
                    @if($search ?? '')
                        <a href="{{ route('service-volumes.index', array_filter(request()->except('search'))) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </form>
                <button type="button" onclick="document.getElementById('import-modal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('Import Excel') }}
                </button>
                <a href="{{ route('service-volumes.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ __('Export Excel') }}
                </a>
                <a href="{{ route('service-volumes.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    {{ __('Add New Service Volume') }}
                </a>
            </div>
        </div>
        <div id="service-volumes-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Service Volume</span> adalah input volume output layanan per periode.
            </p>
            <div class="mb-2">
                <p class="font-semibold mb-1">Contoh:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>OK: jumlah operasi sektio sesarea, appendektomi, dll</li>
                    <li>Lab: jumlah pemeriksaan Darah Lengkap, GDS, dsb</li>
                    <li>RI: patient days / bed days</li>
                </ul>
            </div>
            <div class="mb-2">
                <p class="font-semibold mb-1">Peran di sistem:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Menjadi penyebut dalam perhitungan unit cost per layanan di cost center tersebut</li>
                    <li>Data ini dipakai lagi di modul Unit Cost (versi ringkas muncul di menu Unit Cost → Service Volumes)</li>
                </ul>
            </div>
            <div class="mt-3 pt-3 border-t border-biru-dongker-400">
                <p class="font-semibold mb-1">Merupakan data mentah operasional (source data)</p>
                <p class="mb-2 ml-2">Ini adalah tempat menyimpan angka volume asli dari operasional RS untuk satu periode.</p>
                <div class="mb-2">
                    <p class="font-semibold mb-1">Ciri-cirinya:</p>
                    <ul class="list-disc list-inside space-y-1 ml-2">
                        <li>Berperan sebagai "data sumber" dari HIS / SIMRS / rekap manual</li>
                        <li>Isinya bisa komplet & kotor:
                            <ul class="list-circle list-inside ml-4 space-y-0.5">
                                <li>Semua jenis tindakan, termasuk yang nanti mungkin tidak kamu costing</li>
                                <li>Bisa termasuk kasus yang outlier, komplikasi, paket khusus, dsb</li>
                            </ul>
                        </li>
                        <li>Dipakai oleh:
                            <ul class="list-circle list-inside ml-4 space-y-0.5">
                                <li>Tim keuangan/akuntansi sebagai dasar rekonsiliasi dengan GL</li>
                                <li>Modul lain yang perlu total aktivitas rumah sakit secara keseluruhan</li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <p class="ml-2 italic text-gray-600">Analogi: ini seperti buku absen asli seluruh tindakan di rumah sakit per periode.</p>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('service-volumes.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5">
                    @if($search ?? '')
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    <div>
                        <label for="period_year" class="block text-sm font-medium text-gray-700">{{ __('Year') }}</label>
                        <select id="period_year" name="period_year" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $periodYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="period_month" class="block text-sm font-medium text-gray-700">{{ __('Month') }}</label>
                        <select id="period_month" name="period_month" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            <option value="">{{ __('All Months') }}</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $periodMonth == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="cost_reference_id" class="block text-sm font-medium text-gray-700">{{ __('Service') }}</label>
                        <select id="cost_reference_id" name="cost_reference_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            <option value="">{{ __('All Services') }}</option>
                            @foreach($costReferences as $cr)
                                <option value="{{ $cr->id }}" {{ $costReferenceId == $cr->id ? 'selected' : '' }}>{{ $cr->service_code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tariff_class_id" class="block text-sm font-medium text-gray-700">{{ __('Tariff Class') }}</label>
                        <select id="tariff_class_id" name="tariff_class_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            <option value="">{{ __('All Classes') }}</option>
                            @foreach($tariffClasses as $tc)
                                <option value="{{ $tc->id }}" {{ $tariffClassId == $tc->id ? 'selected' : '' }}>{{ $tc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                            {{ __('Filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($serviceVolumes->count() > 0)
                    <!-- Hidden form for individual delete - placed outside to avoid nested forms -->
                    <form id="single-delete-form" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>

                    <form id="bulk-delete-form" action="{{ route('service-volumes.bulk-delete') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm text-gray-600">
                                {{ __('Select records to delete in bulk') }}
                            </div>
                            <button id="bulk-delete-btn" type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed" disabled onclick="return confirm('{{ __('Are you sure you want to delete the selected service volumes? This action cannot be undone.') }}')">
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Period') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service Code') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service Description') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Tariff Class') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Quantity') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($serviceVolumes as $volume)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <input type="checkbox" name="ids[]" value="{{ $volume->id }}" class="row-checkbox h-4 w-4 text-biru-dongker-800 border-gray-300 rounded">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $volume->period_month }}/{{ $volume->period_year }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $volume->costReference ? $volume->costReference->service_code : '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $volume->costReference ? $volume->costReference->service_description : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $volume->tariffClass ? $volume->tariffClass->name : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($volume->total_quantity, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('service-volumes.show', $volume) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('View') }}" aria-label="{{ __('View') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('service-volumes.edit', $volume) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-biru-dongker-800 hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                                                    </svg>
                                                </a>
                                                <button type="button" 
                                                    onclick="deleteSingleRecord('{{ route('service-volumes.destroy', $volume) }}')"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500" 
                                                    title="{{ __('Delete') }}" 
                                                    aria-label="{{ __('Delete') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1Zm-3 6h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 9Zm3 2v8h2v-8H9Zm4 0v8h2v-8h-2Z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    </form>
                    
                    <div class="mt-6">
                        {{ $serviceVolumes->links() }}
                    </div>
                @else
                    <p class="text-gray-600">{{ __('No service volumes found.') }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="import-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Import Excel') }}</h3>
                    <button onclick="document.getElementById('import-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form action="{{ route('service-volumes.import.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="period_month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Period Month') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="period_month" id="period_month" required class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">{{ __('Select Month') }}</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="period_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Period Year') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="period_year" id="period_year" required class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Select Excel File') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-biru-dongker-50 file:text-biru-dongker-700 hover:file:bg-biru-dongker-100 dark:file:bg-biru-dongker-900 dark:file:text-biru-dongker-300">
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('Format: Service Code, Tariff Class Code (optional), Total Quantity') }}
                        </p>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')" class="btn-secondary">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn-primary">
                            {{ __('Import') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    // Function to handle individual record deletion
    function deleteSingleRecord(deleteUrl) {
        if (confirm('{{ __('Are you sure you want to delete this service volume?') }}')) {
            const form = document.getElementById('single-delete-form');
            form.action = deleteUrl;
            form.submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const bulkBtn = document.getElementById('bulk-delete-btn');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkForm = document.getElementById('bulk-delete-form');
        
        if (!selectAll || !bulkBtn || !bulkForm) return;
        
        // Update button state based on checkbox selection
        function updateButtonState() {
            const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
            bulkBtn.disabled = !anyChecked;
        }
        
        // Select all functionality
        selectAll.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => { cb.checked = selectAll.checked; });
            updateButtonState();
        });
        
        // Individual checkbox change
        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                selectAll.checked = allChecked;
                updateButtonState();
            });
        });
        
        // Initialize state
        updateButtonState();
    });
</script>
@endpush
</div>
@endsection

