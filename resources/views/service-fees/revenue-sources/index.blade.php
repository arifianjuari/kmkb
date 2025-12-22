@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:justify-between md:items-center gap-4 dark:border-gray-700">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Sumber Pendapatan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Master data sumber pendapatan (BPJS, Umum, Asuransi, dll.)</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('service-fees.revenue-sources.seed-defaults') }}" class="btn-secondary" onclick="return confirm('Tambahkan sumber pendapatan default?')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Isi Default
                </a>
                <a href="{{ route('service-fees.revenue-sources.create') }}" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Sumber
                </a>
            </div>
        </div>

        <div class="p-6">
            @if($sources->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Kode</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Nama</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Deskripsi</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($sources as $source)
                                <tr>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">{{ $source->code }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $source->name }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $source->description ?? '-' }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                        @if($source->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('service-fees.revenue-sources.edit', $source) }}" class="text-biru-dongker-600 hover:text-biru-dongker-900" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('service-fees.revenue-sources.destroy', $source) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus sumber pendapatan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Belum ada sumber pendapatan</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Klik "Isi Default" untuk menambahkan sumber pendapatan standar.</p>
                    <div class="mt-6">
                        <a href="{{ route('service-fees.revenue-sources.seed-defaults') }}" class="btn-primary">Isi Default</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
