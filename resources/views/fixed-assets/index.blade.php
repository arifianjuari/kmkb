@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Daftar Aset Tetap') }}</h2>
        <div class="flex items-center space-x-2 flex-wrap gap-2">
            <form method="GET" action="{{ route('fixed-assets.index') }}" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari aset...') }}" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                <select name="jenis_bmn" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Semua Jenis BMN</option>
                    @foreach($jenisBmnOptions as $value => $label)
                        <option value="{{ $value }}" {{ request('jenis_bmn') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="category_id" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary">{{ __('Cari') }}</button>
                @if(request('search') || request('category_id') || request('status') || request('jenis_bmn'))
                    <a href="{{ route('fixed-assets.index') }}" class="btn-secondary">{{ __('Clear') }}</a>
                @endif
            </form>
            <a href="{{ route('fixed-assets.template') }}" class="btn-secondary">{{ __('Template') }}</a>
            <button type="button" onclick="document.getElementById('import-modal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">{{ __('Import') }}</button>
            <a href="{{ route('fixed-assets.depreciation') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">{{ __('Kalkulator Depresiasi') }}</a>
            <a href="{{ route('fixed-assets.create') }}" class="btn-primary">{{ __('+ Tambah Aset') }}</a>
        </div>
    </div>

    <!-- Notifications -->
    @if(session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4 border border-green-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-md bg-red-50 p-4 border border-red-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @if($assets->count() > 0)
                <!-- Bulk Delete Form -->
                <form id="bulk-delete-form" action="{{ route('fixed-assets.bulk-delete') }}" method="POST" onsubmit="return confirm('Yakin hapus aset yang dipilih?')">
                    @csrf
                    @method('DELETE')
                    
                    <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <button type="submit" id="bulk-delete-btn" disabled 
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 013.878.512.75.75 0 11-.256 1.478l-.209-.035-1.005 13.07a3 3 0 01-2.991 2.77H8.084a3 3 0 01-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 01-.256-1.478A48.567 48.567 0 017.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 013.369 0c1.603.051 2.815 1.387 2.815 2.951zm-6.136-1.452a51.196 51.196 0 013.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 00-6 0v-.113c0-.794.609-1.428 1.364-1.452zm-.355 5.945a.75.75 0 10-1.5.058l.347 9a.75.75 0 101.499-.058l-.346-9zm5.48.058a.75.75 0 10-1.498-.058l-.347 9a.75.75 0 001.5.058l.345-9z" clip-rule="evenodd"/></svg>
                                Hapus (<span id="selected-count">0</span>)
                            </button>
                            <button type="button" id="bulk-calculate-btn" disabled onclick="openCalculateModal('selected')"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                Hitung Terpilih
                            </button>
                            <button type="button" onclick="openCalculateModal('all')"
                                    class="inline-flex items-center px-3 py-1.5 border border-green-600 rounded-md shadow-sm text-sm font-medium text-green-700 bg-white hover:bg-green-50">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                Hitung Semua
                            </button>
                        </div>
                        <div class="text-sm text-gray-500">
                            Total: {{ $assets->total() }} aset
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-3 text-center">
                                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-biru-dongker-600 focus:ring-biru-dongker-500">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Aset</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cost Center</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Perolehan</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nilai Buku</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($assets as $index => $asset)
                                    <tr>
                                        <td class="px-2 py-2 text-center">
                                            <input type="checkbox" name="ids[]" value="{{ $asset->id }}" class="asset-checkbox rounded border-gray-300 text-biru-dongker-600 focus:ring-biru-dongker-500">
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $assets->firstItem() + $index }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $asset->asset_code }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">
                                            <div>{{ $asset->name }}</div>
                                            @if($asset->brand || $asset->model)
                                                <div class="text-xs text-gray-500">{{ $asset->brand }} {{ $asset->model }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $asset->assetCategory?->name ?? '-' }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $asset->costCenter?->name ?? '-' }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($asset->acquisition_cost, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-right {{ $asset->is_fully_depreciated ? 'text-red-600' : 'text-green-600' }}">Rp {{ number_format($asset->current_book_value, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                {{ $asset->status == 'active' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $asset->status == 'disposed' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $asset->status == 'sold' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $asset->status == 'in_repair' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            ">{{ $asset->status_display }}</span>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            <div class="flex items-center gap-1">
                                                <a href="{{ route('fixed-assets.show', $asset) }}" class="btn-icon" title="Detail">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                <a href="{{ route('fixed-assets.edit', $asset) }}" class="btn-icon" title="Edit">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/></svg>
                                                </a>
                                                <form action="{{ route('fixed-assets.destroy', $asset) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus aset ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-icon text-red-600" title="Hapus">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 013.878.512.75.75 0 11-.256 1.478l-.209-.035-1.005 13.07a3 3 0 01-2.991 2.77H8.084a3 3 0 01-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 01-.256-1.478A48.567 48.567 0 017.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 013.369 0c1.603.051 2.815 1.387 2.815 2.951zm-6.136-1.452a51.196 51.196 0 013.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 00-6 0v-.113c0-.794.609-1.428 1.364-1.452zm-.355 5.945a.75.75 0 10-1.5.058l.347 9a.75.75 0 101.499-.058l-.346-9zm5.48.058a.75.75 0 10-1.498-.058l-.347 9a.75.75 0 001.5.058l.345-9z" clip-rule="evenodd"/></svg>
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
                <div class="mt-6">{{ $assets->links() }}</div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada aset</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan aset tetap.</p>
                    <div class="mt-4"><a href="{{ route('fixed-assets.create') }}" class="btn-primary">+ Tambah Aset</a></div>
                </div>
            @endif
        </div>
    </div>

    <!-- Calculate Modal -->
    <div id="calculate-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Hitung Depresiasi') }}</h3>
                <button onclick="document.getElementById('calculate-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="calculate-form" action="{{ route('fixed-assets.bulk-calculate') }}" method="POST">
                @csrf
                <input type="hidden" name="calculate_mode" id="calculate-mode" value="all">
                <div id="selected-ids-container"></div>
                <div class="mb-4">
                    <p id="calculate-info" class="text-sm text-gray-600 mb-3"></p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Bulan</label>
                            <select name="period_month" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tahun</label>
                            <select name="period_year" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('calculate-modal').classList.add('hidden')" class="btn-secondary">{{ __('Batal') }}</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        {{ __('Hitung Depresiasi') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="import-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Import Data Aset') }}</h3>
                <button onclick="document.getElementById('import-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('fixed-assets.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">File Excel (.xlsx) <span class="text-red-500">*</span></label>
                    <input type="file" name="file" id="file" accept=".xlsx,.xls" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-biru-dongker-50 file:text-biru-dongker-700 hover:file:bg-biru-dongker-100">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')" class="btn-secondary">{{ __('Batal') }}</button>
                    <button type="submit" class="btn-primary">{{ __('Import') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCalculateModal(mode) {
    document.getElementById('calculate-mode').value = mode;
    const container = document.getElementById('selected-ids-container');
    container.innerHTML = '';
    
    if (mode === 'selected') {
        const checked = document.querySelectorAll('.asset-checkbox:checked');
        document.getElementById('calculate-info').textContent = 'Menghitung depresiasi untuk ' + checked.length + ' aset terpilih.';
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'asset_ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });
    } else {
        document.getElementById('calculate-info').textContent = 'Menghitung depresiasi untuk SEMUA aset aktif.';
    }
    
    document.getElementById('calculate-modal').classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.asset-checkbox');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const bulkCalculateBtn = document.getElementById('bulk-calculate-btn');
    const selectedCount = document.getElementById('selected-count');

    function updateBulkButtons() {
        const checked = document.querySelectorAll('.asset-checkbox:checked');
        const count = checked.length;
        selectedCount.textContent = count;
        bulkDeleteBtn.disabled = count === 0;
        bulkCalculateBtn.disabled = count === 0;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkButtons();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = document.querySelectorAll('.asset-checkbox:checked').length === checkboxes.length;
            if (selectAll) selectAll.checked = allChecked;
            updateBulkButtons();
        });
    });
});
</script>
@endsection
