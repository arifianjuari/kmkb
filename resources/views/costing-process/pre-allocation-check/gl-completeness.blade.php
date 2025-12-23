@extends('layouts.app')

@section('content')
<style>
    .tooltip-container {
        position: relative;
        display: inline-block;
    }
    
    .tooltip-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        margin-left: 6px;
        border-radius: 50%;
        background-color: #e0e7ff;
        color: #4f46e5;
        font-size: 10px;
        font-weight: 600;
        cursor: help;
        transition: all 0.2s;
    }
    
    .tooltip-icon:hover {
        background-color: #4f46e5;
        color: white;
    }
    
    .tooltip-text {
        visibility: hidden;
        opacity: 0;
        width: 280px;
        background-color: #1f2937;
        color: #fff;
        text-align: left;
        border-radius: 8px;
        padding: 12px;
        position: absolute;
        z-index: 1000;
        bottom: 125%;
        left: 50%;
        margin-left: -140px;
        font-size: 13px;
        line-height: 1.5;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: opacity 0.3s, visibility 0.3s;
    }
    
    .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #1f2937 transparent transparent transparent;
    }
    
    .tooltip-container:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }
</style>
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('GL Completeness Check') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-biru-dongker-800 border border-biru-dongker-400 rounded-full w-5 h-5 flex items-center justify-center hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 transition-colors"
                    onclick="const p = document.getElementById('gl-completeness-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is GL Completeness Check?') }}"
                    title="{{ __('What is GL Completeness Check?') }}"
                >
                    i
                </button>
            </div>
        </div>
        
        <div id="gl-completeness-help" class="mb-4 hidden text-xs text-gray-700 bg-biru-dongker-200 border border-biru-dongker-300 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">GL Completeness Check</span> memeriksa kelengkapan data GL Expenses untuk periode tertentu berdasarkan data historis.
            </p>
            <div class="mb-2">
                <p class="font-semibold mb-1">🔹 Bagaimana cara kerjanya?</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Sistem mengambil kombinasi Cost Center × Expense Category dari <strong>12 bulan terakhir</strong></li>
                    <li>Kombinasi tersebut dijadikan acuan (expected) untuk periode yang dicek</li>
                    <li>Missing entries = kombinasi yang ada di 12 bulan terakhir, tapi belum ada di periode ini</li>
                </ul>
            </div>
            <div>
                <p class="font-semibold mb-1">🔹 Mengapa menggunakan data historis?</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Lebih realistis - hanya mengecek kombinasi yang memang pernah terjadi</li>
                    <li>Mengurangi false positive dari kombinasi yang tidak relevan</li>
                    <li>Membantu mendeteksi jika ada data biaya rutin yang terlewat</li>
                </ul>
            </div>
        </div>



        <!-- Filter Form -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('costing-process.pre-allocation-check.gl-completeness') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700">{{ __('Year') }}</label>
                        <select id="year" name="year" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700">{{ __('Month') }}</label>
                        <select id="month" name="month" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                            {{ __('Check') }}
                        </button>
                    </div>
                    <div class="flex items-end">
                        <a href="{{ route('gl-expenses.index', ['period_year' => $year, 'period_month' => $month]) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('View GL Expenses') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-4 mb-6">
            <div class="bg-white overflow-visible shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    {{ __('Total Expected') }}
                                    <div class="tooltip-container">
                                        <span class="tooltip-icon">i</span>
                                        <span class="tooltip-text">
                                            <strong>Total Expected:</strong> Jumlah kombinasi Cost Center × Expense Category yang ditemukan dalam 12 bulan terakhir. Ini menjadi acuan untuk mengecek kelengkapan data periode ini.
                                        </span>
                                    </div>
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($totalExpected, 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-visible shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    {{ __('Found') }}
                                    <div class="tooltip-container">
                                        <span class="tooltip-icon">i</span>
                                        <span class="tooltip-text">
                                            <strong>Found:</strong> Jumlah kombinasi Cost Center × Expense Category yang sudah memiliki data GL Expense untuk periode ini. Semakin tinggi angka ini, semakin lengkap data Anda.
                                        </span>
                                    </div>
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($totalFound, 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-visible shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    {{ __('Missing') }}
                                    <div class="tooltip-container">
                                        <span class="tooltip-icon">i</span>
                                        <span class="tooltip-text">
                                            <strong>Missing:</strong> Jumlah kombinasi Cost Center × Expense Category yang belum memiliki data GL Expense. Data ini perlu dilengkapi sebelum proses alokasi biaya dapat dilakukan.
                                        </span>
                                    </div>
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format(count($missingEntries), 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-visible shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 {{ $completenessPercentage >= 90 ? 'text-green-400' : ($completenessPercentage >= 70 ? 'text-yellow-400' : 'text-red-400') }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    {{ __('Completeness') }}
                                    <div class="tooltip-container">
                                        <span class="tooltip-icon">i</span>
                                        <span class="tooltip-text">
                                            <strong>Completeness:</strong> Persentase kelengkapan data GL Expense. Dihitung dari (Found / Total Expected) × 100%. Target ideal adalah ≥90% (hijau), 70-89% (kuning), <70% (merah).
                                        </span>
                                    </div>
                                </dt>
                                <dd class="text-lg font-medium {{ $completenessPercentage >= 90 ? 'text-green-600' : ($completenessPercentage >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($completenessPercentage, 2) }}%
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Missing Entries Report -->
        @if(count($missingEntries) > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Missing Entries Report') }}</h3>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                        {{ count($missingEntries) }} {{ __('missing') }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('No') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Cost Center') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Expense Category') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($missingEntries as $index => $entry)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-2 text-sm text-gray-900">
                                        <div class="font-medium">{{ $entry['cost_center']->name }}</div>
                                        <div class="text-gray-500 text-xs">{{ $entry['cost_center']->code }} ({{ $entry['cost_center']->type }})</div>
                                    </td>
                                    <td class="px-6 py-2 text-sm text-gray-900">
                                        <div class="font-medium">{{ $entry['expense_category']->account_name }}</div>
                                        <div class="text-gray-500 text-xs">{{ $entry['expense_category']->account_code }}</div>
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm">
                                        <a href="{{ route('gl-expenses.create', ['cost_center_id' => $entry['cost_center']->id, 'expense_category_id' => $entry['expense_category']->id, 'period_year' => $year, 'period_month' => $month]) }}" 
                                           class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                                            {{ __('Add GL Expense') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ __('All GL Expenses are complete for this period!') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Summary by Cost Center -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Summary by Cost Center') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Cost Center') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Expense Categories') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Amount (Rp)') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($summaryByCostCenter as $summary)
                                <tr>
                                    <td class="px-6 py-2 text-sm text-gray-900">
                                        <div class="font-medium">{{ $summary->name }}</div>
                                        <div class="text-gray-500 text-xs">{{ $summary->code }}</div>
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $summary->type == 'support' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $summary->type == 'support' ? __('Support') : __('Revenue') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ $summary->expense_category_count }} / {{ $expenseCategories->count() }}
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ number_format($summary->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-2 text-sm text-gray-500 text-center">
                                        {{ __('No GL Expenses found for this period.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary by Expense Category -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Summary by Expense Category') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Expense Category') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Cost Centers') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Amount (Rp)') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($summaryByExpenseCategory as $summary)
                                <tr>
                                    <td class="px-6 py-2 text-sm text-gray-900">
                                        <div class="font-medium">{{ $summary->account_name }}</div>
                                        <div class="text-gray-500 text-xs">{{ $summary->account_code }}</div>
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ $summary->cost_center_count }} / {{ $costCenters->count() }}
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ number_format($summary->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-2 text-sm text-gray-500 text-center">
                                        {{ __('No GL Expenses found for this period.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
