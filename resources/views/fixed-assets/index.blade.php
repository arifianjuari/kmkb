@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Daftar Aset Tetap') }}</h2>
        <div class="flex items-center space-x-2 flex-wrap gap-2">
            <form method="GET" action="{{ route('fixed-assets.index') }}" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari aset...') }}" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
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
                @if(request('search') || request('category_id') || request('status'))
                    <a href="{{ route('fixed-assets.index') }}" class="btn-secondary">{{ __('Clear') }}</a>
                @endif
            </form>
            <a href="{{ route('fixed-assets.template') }}" class="btn-secondary">{{ __('Template') }}</a>
            <button type="button" onclick="document.getElementById('import-modal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">{{ __('Import') }}</button>
            <a href="{{ route('fixed-assets.depreciation') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">{{ __('Kalkulator Depresiasi') }}</a>
            <a href="{{ route('fixed-assets.create') }}" class="btn-primary">{{ __('+ Tambah Aset') }}</a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @if($assets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
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
@endsection
