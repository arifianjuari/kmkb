@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:justify-between md:items-center gap-4 dark:border-gray-700">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Data Pendapatan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Import dan kelola data pendapatan per periode</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('service-fees.revenue-records.template') }}" class="btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Template
                </a>
                <a href="{{ route('service-fees.revenue-records.import') }}" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import Data
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun</label>
                    <input type="number" id="year" name="year" value="{{ $year ?? date('Y') }}" min="2020" max="2099"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                </div>
                <div>
                    <label for="source_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sumber</label>
                    <select id="source_id" name="source_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                        <option value="">Semua Sumber</option>
                        @foreach($sources ?? [] as $source)
                            <option value="{{ $source->id }}" {{ ($sourceId ?? '') == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2 col-span-2">
                    <button type="submit" class="btn-primary">Filter</button>
                    <a href="{{ route('service-fees.revenue-records.index') }}" class="btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <!-- Summary -->
        @if(isset($summary))
        <div class="p-6 border-b border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Bruto</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($summary['gross'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Netto</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($summary['net'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Klaim</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($summary['claims'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Table -->
        <div class="p-6">
            @if(isset($records) && $records->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Periode</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Sumber</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Kategori</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Bruto</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Netto</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Klaim</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($records as $record)
                                <tr>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $record->period_label }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $record->source->name ?? '-' }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">{{ $record->category_label }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">Rp {{ number_format($record->gross_revenue, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-right text-green-600">Rp {{ number_format($record->net_revenue, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($record->claim_count) }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('service-fees.revenue-records.edit', $record) }}" class="text-biru-dongker-600 hover:text-biru-dongker-900" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('service-fees.revenue-records.destroy', $record) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus data pendapatan ini?')">
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
                    {{ $records->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Belum ada data pendapatan</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Import data pendapatan dari Excel untuk memulai.</p>
                    <div class="mt-6">
                        <a href="{{ route('service-fees.revenue-records.import') }}" class="btn-primary">Import Data</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
