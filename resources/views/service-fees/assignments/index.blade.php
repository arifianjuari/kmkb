@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:justify-between md:items-center gap-4 dark:border-gray-700">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Penugasan Jasa</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pengaturan role tenaga kesehatan per layanan/tindakan</p>
            </div>
            <a href="{{ route('service-fees.assignments.create', isset($costReferenceId) ? ['cost_reference_id' => $costReferenceId] : []) }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Penugasan
            </a>
        </div>

        <!-- Search -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <form method="GET" class="flex gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cari Layanan</label>
                    <input type="text" id="search" name="search" value="{{ $search ?? '' }}" placeholder="Nama atau kode layanan..."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary">Cari</button>
                    <a href="{{ route('service-fees.assignments.index') }}" class="btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="p-6">
            @if($assignments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Layanan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Role</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Partisipasi</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Jumlah</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Point Efektif</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($assignments as $assignment)
                                <tr>
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $assignment->costReference->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $assignment->costReference->code ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $assignment->serviceFeeIndex->role_label ?? '-' }}
                                        <span class="text-xs text-gray-500 block">{{ $assignment->serviceFeeIndex->category_label ?? '' }}</span>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ number_format($assignment->participation_pct, 0) }}%</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $assignment->headcount }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-center font-semibold text-biru-dongker-600">{{ number_format($assignment->effective_points, 2) }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                        @if($assignment->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('service-fees.assignments.edit', $assignment) }}" class="text-biru-dongker-600 hover:text-biru-dongker-900" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('service-fees.assignments.destroy', $assignment) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus penugasan ini?')">
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
                    {{ $assignments->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Belum ada penugasan jasa</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tentukan role tenaga kesehatan untuk setiap layanan.</p>
                    <div class="mt-6">
                        <a href="{{ route('service-fees.assignments.create') }}" class="btn-primary">Tambah Penugasan</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
