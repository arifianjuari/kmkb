@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto px-4">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Rekap Pengeluaran') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('expense-breakdown-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('Info') }}"
                    title="{{ __('Info') }}"
                >
                    i
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('expense-breakdown.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    {{ __('Export Excel') }}
                </a>
            </div>
        </div>
        
        <div id="expense-breakdown-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Rekap Pengeluaran</span> menampilkan detail GL Expenses yang dipecah berdasarkan deskripsi.
            </p>
            <p class="mb-2">
                Data ini berasal dari GL Expenses yang saat import memiliki deskripsi tergabung dengan penanda "; " (titik koma).
            </p>
            <div class="mb-2">
                <p class="font-semibold mb-1">🔹 Kolom yang ditampilkan:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li><strong>Account Code</strong> - Kode akun dari Expense Category</li>
                    <li><strong>Description</strong> - Deskripsi detail (dipecah dari deskripsi gabungan)</li>
                    <li><strong>Subtotal</strong> - Total per Account Code</li>
                    <li><strong>Total</strong> - Total per baris deskripsi</li>
                    <li><strong>JAN - DES</strong> - Nilai per bulan</li>
                </ul>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('expense-breakdown.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label for="period_year" class="block text-sm font-medium text-gray-700">{{ __('Year') }}</label>
                        <select id="period_year" name="period_year" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $periodYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="cost_center_id" class="block text-sm font-medium text-gray-700">{{ __('Cost Center') }}</label>
                        <select id="cost_center_id" name="cost_center_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            <option value="">{{ __('All Cost Centers') }}</option>
                            @foreach($costCenters as $cc)
                                <option value="{{ $cc->id }}" {{ $costCenterId == $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                            {{ __('Filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Data Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if(count($groupedData) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">{{ __('Account Code') }}</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Description') }}</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Subtotal') }}</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total') }}</th>
                                    @foreach(['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOP', 'DES'] as $monthName)
                                        <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $monthName }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($groupedData as $accountCode => $group)
                                    @php $isFirst = true; @endphp
                                    @foreach($group['items'] as $index => $item)
                                        <tr class="{{ $loop->first ? 'border-t-2 border-gray-300' : '' }}">
                                            @if($isFirst)
                                                <td rowspan="{{ count($group['items']) }}" class="px-3 py-1.5 whitespace-nowrap text-gray-900 font-medium align-top sticky left-0 bg-white z-10 border-r border-gray-200">
                                                    {{ $accountCode }}
                                                </td>
                                                @php $isFirst = false; @endphp
                                            @endif
                                            <td class="px-3 py-1.5 text-gray-700 max-w-xs truncate" title="{{ $item['description'] }}">{{ $item['description'] }}</td>
                                            @if($index === 0)
                                                <td rowspan="{{ count($group['items']) }}" class="px-3 py-1.5 whitespace-nowrap text-right text-gray-900 font-bold align-top bg-yellow-50">
                                                    {{ number_format($group['subtotal'], 0, ',', '.') }}
                                                </td>
                                            @endif
                                            <td class="px-3 py-1.5 whitespace-nowrap text-right text-gray-900">{{ number_format($item['total'], 0, ',', '.') }}</td>
                                            @for($m = 1; $m <= 12; $m++)
                                                <td class="px-2 py-1.5 whitespace-nowrap text-right text-gray-700 {{ $item['months'][$m] > 0 ? '' : 'text-gray-300' }}">
                                                    {{ $item['months'][$m] > 0 ? number_format($item['months'][$m], 0, ',', '.') : '-' }}
                                                </td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                @endforeach
                                
                                <!-- Grand Total Row -->
                                <tr class="bg-biru-dongker-100 font-bold border-t-2 border-biru-dongker-300">
                                    <td colspan="2" class="px-3 py-2 text-gray-900 sticky left-0 bg-biru-dongker-100 z-10">{{ __('GRAND TOTAL') }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-right text-gray-900"></td>
                                    <td class="px-3 py-2 whitespace-nowrap text-right text-biru-dongker-800">{{ number_format($grandTotal, 0, ',', '.') }}</td>
                                    @for($m = 1; $m <= 12; $m++)
                                        <td class="px-2 py-2 whitespace-nowrap text-right text-biru-dongker-800">
                                            {{ $monthsGrandTotal[$m] > 0 ? number_format($monthsGrandTotal[$m], 0, ',', '.') : '-' }}
                                        </td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-600">{{ __('Tidak ada data untuk periode yang dipilih.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
