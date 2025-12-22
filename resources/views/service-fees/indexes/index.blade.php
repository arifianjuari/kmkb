@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:justify-between md:items-center gap-4 dark:border-gray-700">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Indeks Jasa</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pengaturan indeks/scoring per kategori profesional dan role</p>
            </div>
            <a href="{{ route('service-fees.indexes.create', isset($configId) ? ['config_id' => $configId] : []) }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Indeks
            </a>
        </div>

        <!-- Filters -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="config_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfigurasi</label>
                    <select id="config_id" name="config_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                        <option value="">Semua Konfigurasi</option>
                        @foreach($configs as $config)
                            <option value="{{ $config->id }}" {{ $configId == $config->id ? 'selected' : '' }}>{{ $config->name }} ({{ $config->period_year }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                    <select id="category" name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ $category == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary">Filter</button>
                    <a href="{{ route('service-fees.indexes.index') }}" class="btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="p-6">
            @if($indexes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Konfigurasi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Kategori</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Profesional</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Role</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Base Index</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Final Index</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($indexes as $index)
                                <tr>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $index->config->name ?? '-' }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $index->category === 'medis' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $index->category === 'keperawatan' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $index->category === 'penunjang' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $index->category === 'manajemen' ? 'bg-purple-100 text-purple-800' : '' }}">
                                            {{ $index->category_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $index->professional_category }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $index->role_label }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ number_format($index->base_index, 2) }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-center font-semibold text-biru-dongker-600">{{ number_format($index->final_index, 2) }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                        @if($index->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('service-fees.indexes.edit', $index) }}" class="text-biru-dongker-600 hover:text-biru-dongker-900" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('service-fees.indexes.destroy', $index) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus indeks ini?')">
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

                <div class="mt-4">
                    {{ $indexes->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Belum ada indeks jasa</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Buat indeks untuk setiap role tenaga kesehatan.</p>
                    <div class="mt-6">
                        <a href="{{ route('service-fees.indexes.create') }}" class="btn-primary">Tambah Indeks</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
