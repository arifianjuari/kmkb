@extends('layouts.app')

@section('content')
@php
    $costTypeTabs = [
        'all' => __('All Cost Types'),
        'fixed' => __('Fixed'),
        'variable' => __('Variable'),
        'semi_variable' => __('Semi Variable'),
    ];
    
    $allocationCategoryTabs = [
        'all' => __('All Categories'),
        'gaji' => __('Gaji'),
        'bhp_medis' => __('BHP Medis'),
        'bhp_non_medis' => __('BHP Non Medis'),
        'depresiasi' => __('Depresiasi'),
        'lain_lain' => __('Lain-lain'),
    ];
@endphp
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Expense Categories') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('expense-categories-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is Expense Category?') }}"
                    title="{{ __('What is Expense Category?') }}"
                >
                    i
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <form method="GET" action="{{ route('expense-categories.index') }}" class="flex items-center space-x-2">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('Search...') }}" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                    <select name="cost_type" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                        <option value="">{{ __('All Cost Types') }}</option>
                        <option value="fixed" {{ $costType == 'fixed' ? 'selected' : '' }}>{{ __('Fixed') }}</option>
                        <option value="variable" {{ $costType == 'variable' ? 'selected' : '' }}>{{ __('Variable') }}</option>
                        <option value="semi_variable" {{ $costType == 'semi_variable' ? 'selected' : '' }}>{{ __('Semi Variable') }}</option>
                    </select>
                    <select name="allocation_category" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                        <option value="">{{ __('All Categories') }}</option>
                        <option value="gaji" {{ $allocationCategory == 'gaji' ? 'selected' : '' }}>{{ __('Gaji') }}</option>
                        <option value="bhp_medis" {{ $allocationCategory == 'bhp_medis' ? 'selected' : '' }}>{{ __('BHP Medis') }}</option>
                        <option value="bhp_non_medis" {{ $allocationCategory == 'bhp_non_medis' ? 'selected' : '' }}>{{ __('BHP Non Medis') }}</option>
                        <option value="depresiasi" {{ $allocationCategory == 'depresiasi' ? 'selected' : '' }}>{{ __('Depresiasi') }}</option>
                        <option value="lain_lain" {{ $allocationCategory == 'lain_lain' ? 'selected' : '' }}>{{ __('Lain-lain') }}</option>
                    </select>
                    <select name="is_active" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="1" {{ $isActive === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="0" {{ $isActive === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                        {{ __('Filter') }}
                    </button>
                    @if($search || $costType || $allocationCategory || $isActive !== null)
                        <a href="{{ route('expense-categories.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </form>
                <a href="{{ route('expense-categories.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    {{ __('Export Excel') }}
                </a>
                @if(!auth()->user()?->isObserver())
                <button
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'import-expense-category-modal')"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                >
                    {{ __('Import Excel') }}
                </button>
                <a href="{{ route('expense-categories.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                    {{ __('Add New Cost Element') }}
                </a>
                @endif
            </div>
        </div>
        <div id="expense-categories-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-1">
                <span class="font-semibold">Cost Element (COA Mapping)</span> adalah pengelompokan akun biaya (beban) rumah sakit, misalnya gaji, BHP medis, BHP non medis, depresiasi, dan biaya lain-lain.
            </p>
            <p class="mb-2">
                Kode 4 digit dengan awalan angka <span class="font-mono">5</span> mengikuti struktur chart of accounts, di mana <span class="font-mono">5xxx</span> berarti akun biaya (expenses), dan dua digit berikutnya (mis. <span class="font-mono">51xx</span>, <span class="font-mono">52xx</span>) membedakan kelompok seperti gaji, BHP medis, BHP non medis, depresiasi, dan lain-lain.
            </p>
            <div>
                <p class="font-semibold mb-1">Cost type menjelaskan perilaku biaya terhadap volume layanan:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li><span class="font-semibold">Fixed Cost (FC)</span>: tidak berubah signifikan ketika volume pasien naik/turun (gaji tetap, depresiasi alat, sewa gedung)</li>
                    <li><span class="font-semibold">Variable Cost (VC)</span>: naik-turun sebanding dengan jumlah tindakan/pasien (BHP, obat, bahan sekali pakai, makan pasien)</li>
                    <li><span class="font-semibold">Semi Fixed Cost</span>: awalnya tetap, lalu loncat ke level baru setelah melewati batas volume tertentu (misal menambah 1 perawat ketika pasien &gt; 5)</li>
                </ul>
            </div>
        </div>
        


        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="mb-6 space-y-4">
                    {{-- Cost Type Tabs (Indigo) --}}
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider">{{ __('Cost Type') }}</p>
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($costTypeTabs as $key => $label)
                                @php
                                    $isActive = ($key === 'all' && !$costType) || ($key === $costType);
                                    $urlParams = request()->except('cost_type', 'page');
                                    if ($key !== 'all') {
                                        $urlParams['cost_type'] = $key;
                                    }
                                    $tabUrl = route('expense-categories.index', $urlParams);
                                @endphp
                                <a
                                    href="{{ $tabUrl }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 border rounded-full text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-biru-dongker-700 {{ $isActive ? 'bg-biru-dongker-800 text-white border-biru-dongker-800' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}"
                                >
                                    <span>{{ $label }}</span>
                                    <span class="text-xs font-semibold {{ $isActive ? 'text-white/80' : 'text-gray-500' }}">
                                        {{ $costTypeCounts[$key] ?? 0 }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Allocation Category Tabs (Emerald/Green) --}}
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider">{{ __('Allocation Category') }}</p>
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($allocationCategoryTabs as $key => $label)
                                @php
                                    $isActive = ($key === 'all' && !$allocationCategory) || ($key === $allocationCategory);
                                    $urlParams = request()->except('allocation_category', 'page');
                                    if ($key !== 'all') {
                                        $urlParams['allocation_category'] = $key;
                                    }
                                    $tabUrl = route('expense-categories.index', $urlParams);
                                @endphp
                                <a
                                    href="{{ $tabUrl }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 border rounded-full text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-500 {{ $isActive ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}"
                                >
                                    <span>{{ $label }}</span>
                                    <span class="text-xs font-semibold {{ $isActive ? 'text-white/80' : 'text-gray-500' }}">
                                        {{ $allocationCategoryCounts[$key] ?? 0 }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @if($expenseCategories->count() > 0)
                    @if(!auth()->user()?->isObserver())
                    <form id="bulk-delete-form" action="{{ route('expense-categories.bulk-destroy') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm text-gray-600">
                                {{ __('Select records to delete in bulk') }}
                            </div>
                            <button id="bulk-delete-btn" type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed" disabled onclick="return confirm('{{ __('Are you sure you want to delete the selected expense categories? This action cannot be undone.') }}')">
                                {{ __('Delete Selected') }}
                            </button>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    @if(!auth()->user()?->isObserver())
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input id="select-all" type="checkbox" class="h-4 w-4 text-biru-dongker-800 border-gray-300 rounded">
                                    </th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Account Code') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Account Name') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Cost Type') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Allocation Category') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($expenseCategories as $category)
                                    <tr>
                                        @if(!auth()->user()?->isObserver())
                                        <td class="px-6 py-2 whitespace-nowrap text-sm">
                                            <input type="checkbox" name="ids[]" value="{{ $category->id }}" class="row-checkbox h-4 w-4 text-biru-dongker-800 border-gray-300 rounded">
                                        </td>
                                        @endif
                                        <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $category->account_code }}</td>
                                        <td class="px-6 py-2 text-sm text-gray-900">{{ $category->account_name }}</td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $category->cost_type)) }}</td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $category->allocation_category)) }}</td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $category->is_active ? __('Active') : __('Inactive') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('expense-categories.show', $category) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('View') }}" aria-label="{{ __('View') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                                                    </svg>
                                                </a>
                                                @if(!auth()->user()?->isObserver())
                                                <a href="{{ route('expense-categories.edit', $category) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-biru-dongker-800 hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('expense-categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this cost element?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                            <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1Zm-3 6h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 9Zm3 2v8h2v-8H9Zm4 0v8h2v-8h-2Z"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(!auth()->user()?->isObserver())
                    </form>
                    @endif
                    
                    <div class="mt-6">
                        {{ $expenseCategories->links() }}
                    </div>
                @else
                    <p class="text-gray-600">{{ __('No cost elements found.') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <x-modal name="import-expense-category-modal" :show="false" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Import Expense Categories') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Upload an Excel file to import expense categories. Existing records with the same Account Code will be updated.') }}
            </p>

            <div class="mt-4">
                <a href="{{ route('expense-categories.template') }}" class="text-sm text-blue-600 hover:text-blue-900 underline">
                    {{ __('Download Template') }}
                </a>
            </div>

            <form method="POST" action="{{ route('expense-categories.import') }}" enctype="multipart/form-data" class="mt-6">
                @csrf
                
                <div>
                    <x-input-label for="file" :value="__('Excel File')" />
                    <input id="file" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="file" accept=".xlsx, .xls" required>
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button class="ml-3">
                        {{ __('Import') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
@endsection

@push('scripts')
@if(!auth()->user()?->isObserver())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const bulkBtn = document.getElementById('bulk-delete-btn');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkForm = document.getElementById('bulk-delete-form');
        
        // Update button state based on checkbox selection
        function updateButtonState() {
            const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
            if (bulkBtn) {
                bulkBtn.disabled = !anyChecked;
            }
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
                    alert('{{ __('Please select at least one item to delete.') }}');
                    return false;
                }
                
                if (!confirm('{{ __('Are you sure you want to delete the selected cost elements? This action cannot be undone.') }}')) {
                    e.preventDefault();
                    return false;
                }
            });
        }
        
        // Initialize state
        updateButtonState();
    });
</script>
@endif
@endpush

