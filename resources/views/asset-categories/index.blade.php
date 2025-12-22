@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Kategori Aset') }}</h2>
        <div class="flex items-center space-x-2">
            <form method="GET" action="{{ route('asset-categories.index') }}" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari...') }}" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                <select name="type" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Semua Tipe</option>
                    @foreach($typeOptions as $value => $label)
                        <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary">{{ __('Cari') }}</button>
                @if(request('search') || request('type'))
                    <a href="{{ route('asset-categories.index') }}" class="btn-secondary">{{ __('Clear') }}</a>
                @endif
            </form>
            <a href="{{ route('asset-categories.create') }}" class="btn-primary">{{ __('+ Tambah Kategori') }}</a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @if($categories->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Umur Ekonomis</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($categories as $index => $category)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $categories->firstItem() + $index }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{{ $category->code ?: '-' }}</td>
                                    <td class="px-6 py-2 text-sm text-gray-900">{{ $category->name }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            {{ $category->type == 'alkes' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $category->type == 'sarpras' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $category->type == 'bangunan' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $category->type == 'kendaraan' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $category->type == 'it' ? 'bg-cyan-100 text-cyan-800' : '' }}
                                            {{ $category->type == 'lainnya' ? 'bg-gray-100 text-gray-800' : '' }}
                                        ">{{ $category->type_display }}</span>
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-center">{{ $category->default_useful_life_years }} tahun</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-center">
                                        @if($category->is_active)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('asset-categories.edit', $category) }}" class="btn-icon" title="{{ __('Edit') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('asset-categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus kategori ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon text-red-600 hover:text-red-900" title="{{ __('Hapus') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 013.878.512.75.75 0 11-.256 1.478l-.209-.035-1.005 13.07a3 3 0 01-2.991 2.77H8.084a3 3 0 01-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 01-.256-1.478A48.567 48.567 0 017.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 013.369 0c1.603.051 2.815 1.387 2.815 2.951zm-6.136-1.452a51.196 51.196 0 013.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 00-6 0v-.113c0-.794.609-1.428 1.364-1.452zm-.355 5.945a.75.75 0 10-1.5.058l.347 9a.75.75 0 101.499-.058l-.346-9zm5.48.058a.75.75 0 10-1.498-.058l-.347 9a.75.75 0 001.5.058l.345-9z" clip-rule="evenodd"/>
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
                <div class="mt-6">
                    {{ $categories->links() }}
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada kategori</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan kategori aset.</p>
                    <div class="mt-4">
                        <a href="{{ route('asset-categories.create') }}" class="btn-primary">+ Tambah Kategori</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
