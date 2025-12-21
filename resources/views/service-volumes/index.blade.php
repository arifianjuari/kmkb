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
                    @if($category ?? '')
                        <input type="hidden" name="category" value="{{ $category }}">
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
                        <label for="status" class="block text-sm font-medium text-gray-700">Pemeriksaan Penunjang</label>
                        <select id="status" name="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            <option value="">Semua Status</option>
                            <option value="Ralan" {{ ($status ?? '') === 'Ralan' ? 'selected' : '' }}>Rawat Jalan</option>
                            <option value="Ranap" {{ ($status ?? '') === 'Ranap' ? 'selected' : '' }}>Rawat Inap</option>
                        </select>
                    </div>

                    <div>
                        <label for="kd_poli" class="block text-sm font-medium text-gray-700">Poliklinik</label>
                        <select id="kd_poli" name="kd_poli" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            <option value="">Semua Poli</option>
                            @if(isset($poliOptions))
                                @foreach($poliOptions as $poli)
                                    <option value="{{ $poli->kd_poli }}" {{ ($kdPoli ?? '') === $poli->kd_poli ? 'selected' : '' }}>{{ $poli->nm_poli ?? $poli->kd_poli }} ({{ $poli->total }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div>
                        <label for="kd_bangsal" class="block text-sm font-medium text-gray-700">Bangsal</label>
                        <select id="kd_bangsal" name="kd_bangsal" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            <option value="">Semua Bangsal</option>
                            @if(isset($bangsalOptions))
                                @foreach($bangsalOptions as $bangsal)
                                    <option value="{{ $bangsal->kd_bangsal }}" {{ ($kdBangsal ?? '') === $bangsal->kd_bangsal ? 'selected' : '' }}>{{ $bangsal->nm_bangsal ?? $bangsal->kd_bangsal }} ({{ $bangsal->total }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('service-volumes.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        {{-- Category Tabs --}}
        <div class="mb-4">
            <div class="flex flex-wrap items-center gap-2.5">
                @php
                    $tabs = array_merge(['all' => 'All Categories'], $categoryOptions);
                @endphp
                
                @foreach($tabs as $key => $label)
                    @php
                        $tabKey = $key === 'all' ? null : $key;
                        $isActiveTab = $category == $tabKey;
                        $urlParams = request()->except('category', 'page');
                        if ($tabKey) {
                            $urlParams['category'] = $tabKey;
                        }
                        $tabUrl = route('service-volumes.index', $urlParams);
                        $countKey = $key; // Matches keys in $categoryCounts
                    @endphp
                    <a
                        href="{{ $tabUrl }}"
                        class="inline-flex items-center gap-2 px-2.5 py-1 border rounded-full text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-biru-dongker-700 {{ $isActiveTab ? 'bg-biru-dongker-800 text-white border-biru-dongker-800' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}"
                    >
                        <span>{{ __($label) }}</span>
                        @if(isset($categoryCounts))
                            <span class="ml-1 text-[10px] font-semibold {{ $isActiveTab ? 'text-white/80' : 'text-gray-500' }}">
                                {{ $categoryCounts[$countKey] ?? 0 }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>



        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow border-b border-gray-200 sm:rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 z-10 bg-gray-50 shadow-sm min-w-[200px]">
                                            {{ __('Service / Category') }}
                                        </th>
                                        @foreach(range(1, 12) as $m)
                                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[60px]">
                                                {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                                            </th>
                                        @endforeach
                                        <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[80px] font-bold bg-gray-100">
                                            {{ __('Total') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($services as $service)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 sticky left-0 z-10 bg-white shadow-sm border-r group-hover:bg-gray-50">
                                                <div class="flex flex-col">
                                                    <span class="truncate" title="{{ $service->service_description }}">{{ $service->service_description }}</span>
                                                    <span class="text-xs text-gray-500">{{ $service->service_code }} • {{ $categoryOptions[$service->category] ?? '-' }}</span>
                                                </div>
                                            </td>
                                            
                                            @php
                                                $rowTotal = 0;
                                            @endphp

                                            @foreach(range(1, 12) as $m)
                                                @php
                                                    $val = $volumeMap[$service->id][$m] ?? 0;
                                                    $rowTotal += $val;
                                                @endphp
                                                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500 text-center border-r border-gray-100">
                                                    {{ $val > 0 ? number_format($val, 0, ',', '.') : '-' }}
                                                </td>
                                            @endforeach
                                            
                                            <td class="px-3 py-3 whitespace-nowrap text-sm font-bold text-gray-900 text-right bg-gray-50">
                                                {{ $rowTotal > 0 ? number_format($rowTotal, 0, ',', '.') : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    @if($services->count() === 0)
                                        <tr>
                                            <td colspan="14" class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-center">
                                                {{ __('No services found matching your criteria.') }}
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                @if($services->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $services->links() }}
                    </div>
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

