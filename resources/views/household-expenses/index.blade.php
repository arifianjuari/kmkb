@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Biaya Alat Rumah Tangga') }}</h2>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('household-expenses.template') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    {{ __('Template') }}
                </a>
                <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('Import') }}
                </button>
                <a href="{{ route('household-expenses.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ __('Export') }}
                </a>
                <a href="{{ route('household-expenses.create', request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    {{ __('+ Tambah Data') }}
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('household-expenses.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5">
                    <div>
                        <label for="period_year" class="block text-sm font-medium text-gray-700">{{ __('Year') }}</label>
                        <select id="period_year" name="period_year" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-biru-dongker-500 focus:border-biru-dongker-500 sm:text-sm rounded-md">
                            @for($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $periodYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="period_month" class="block text-sm font-medium text-gray-700">{{ __('Month') }}</label>
                        <select id="period_month" name="period_month" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-biru-dongker-500 focus:border-biru-dongker-500 sm:text-sm rounded-md">
                            <option value="">Semua Bulan</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $periodMonth == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="cost_center_id" class="block text-sm font-medium text-gray-700">{{ __('Cost Center') }}</label>
                        <select id="cost_center_id" name="cost_center_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-biru-dongker-500 focus:border-biru-dongker-500 sm:text-sm rounded-md">
                            <option value="">Semua Cost Center</option>
                            @foreach($costCenters as $cc)
                                <option value="{{ $cc->id }}" {{ $costCenterId == $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">{{ __('Search Item') }}</label>
                        <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Nama item..." class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-biru-dongker-500 focus:border-biru-dongker-500 sm:text-sm rounded-md">
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="px-4 py-2 bg-biru-dongker-600 text-white rounded-md text-sm hover:bg-biru-dongker-700">
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('household-expenses.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="bg-gradient-to-r from-biru-dongker-800 to-biru-dongker-900 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm opacity-80">Total Biaya (Filter Aktif)</p>
                    <p class="text-3xl font-bold">Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm opacity-80">Periode</p>
                    <p class="text-lg font-semibold">{{ $periodMonth ? date('F', mktime(0, 0, 0, $periodMonth, 1)) : 'Semua Bulan' }} {{ $periodYear }}</p>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($expenses->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost Center</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php $months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']; @endphp
                                @foreach($expenses as $index => $expense)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $expenses->firstItem() + $index }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ $expense->costCenter->name ?? '-' }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ $expense->householdItem->name ?? '-' }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $expense->householdItem->unit ?? '-' }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-center text-gray-500">{{ $months[$expense->period_month] ?? $expense->period_month }} {{ $expense->period_year }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-900">{{ number_format($expense->quantity, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-900">{{ number_format($expense->unit_price, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-right font-medium text-gray-900">{{ number_format($expense->total_amount, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-center">
                                            <a href="{{ route('household-expenses.edit', $expense) }}" class="text-biru-dongker-600 hover:text-biru-dongker-900 mr-2" title="Edit">
                                                <svg class="inline-block w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </a>
                                            <form action="{{ route('household-expenses.destroy', $expense) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                    <svg class="inline-block w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $expenses->links() }}
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada data</h3>
                        <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan data biaya rumah tangga.</p>
                        <div class="mt-4">
                            <a href="{{ route('household-expenses.create') }}" class="inline-flex items-center px-4 py-2 bg-biru-dongker-600 text-white rounded-md text-sm hover:bg-biru-dongker-700">
                                + Tambah Data Baru
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Import Data Biaya</h3>
            <form action="{{ route('household-expenses.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Excel (.xlsx)</label>
                    <input type="file" name="file" accept=".xlsx,.xls" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-biru-dongker-50 file:text-biru-dongker-700 hover:file:bg-biru-dongker-100">
                    <p class="mt-2 text-xs text-gray-500">Pastikan Anda sudah mengisi Master Item dan Cost Center terlebih dahulu.</p>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-biru-dongker-600 text-white rounded-md text-sm hover:bg-biru-dongker-700">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
