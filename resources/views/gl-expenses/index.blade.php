@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('GL Expenses') }}</h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-indigo-600 border border-indigo-200 rounded-full w-5 h-5 flex items-center justify-center hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                    onclick="const p = document.getElementById('gl-expenses-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="{{ __('What is GL Expense?') }}"
                    title="{{ __('What is GL Expense?') }}"
                >
                    i
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('gl-expenses.import') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    {{ __('Import Excel') }}
                </a>
                <a href="{{ route('gl-expenses.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    {{ __('Export Excel') }}
                </a>
                <a href="{{ route('gl-expenses.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    {{ __('Add New GL Expense') }}
                </a>
            </div>
        </div>
        <div id="gl-expenses-help" class="mb-4 hidden text-xs text-gray-700 bg-indigo-50 border border-indigo-100 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">GL Expenses</span> adalah modul untuk mengimpor/menginput data biaya riil dari sistem akuntansi (GL) per periode.
            </p>
            <div class="mb-2">
                <p class="font-semibold mb-1">Isi utama:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Periode (bulan/tahun)</li>
                    <li>Cost center</li>
                    <li>Expense category (dan akun GL)</li>
                    <li>Nilai biaya</li>
                </ul>
            </div>
            <div>
                <p class="font-semibold mb-1">Peran di sistem:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Menjadi total biaya awal tiap cost center sebelum dialokasikan</li>
                    <li>Di sinilah biaya overhead pertama kali masuk (di cost center support)</li>
                </ul>
            </div>
        </div>
        
        @if(session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('errors') && is_array(session('errors')))
            <div class="mb-6 rounded-md bg-yellow-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-800">Peringatan Import:</p>
                        <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside">
                            @foreach(array_slice(session('errors'), 0, 5) as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('gl-expenses.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5">
                    <div>
                        <label for="period_year" class="block text-sm font-medium text-gray-700">{{ __('Year') }}</label>
                        <select id="period_year" name="period_year" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $periodYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="period_month" class="block text-sm font-medium text-gray-700">{{ __('Month') }}</label>
                        <select id="period_month" name="period_month" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">{{ __('All Months') }}</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $periodMonth == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="cost_center_id" class="block text-sm font-medium text-gray-700">{{ __('Cost Center') }}</label>
                        <select id="cost_center_id" name="cost_center_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">{{ __('All Cost Centers') }}</option>
                            @foreach($costCenters as $cc)
                                <option value="{{ $cc->id }}" {{ $costCenterId == $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="expense_category_id" class="block text-sm font-medium text-gray-700">{{ __('Expense Category') }}</label>
                        <select id="expense_category_id" name="expense_category_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($expenseCategories as $ec)
                                <option value="{{ $ec->id }}" {{ $expenseCategoryId == $ec->id ? 'selected' : '' }}>{{ $ec->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            {{ __('Filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($glExpenses->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Period') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Cost Center') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Expense Category') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount (Rp)') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($glExpenses as $expense)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $expense->period_month }}/{{ $expense->period_year }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $expense->costCenter ? $expense->costCenter->name : '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $expense->expenseCategory ? $expense->expenseCategory->account_name : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($expense->amount, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('gl-expenses.show', $expense) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500" title="{{ __('View') }}" aria-label="{{ __('View') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('gl-expenses.edit', $expense) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('gl-expenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this GL expense?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                            <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1Zm-3 6h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 9Zm3 2v8h2v-8H9Zm4 0v8h2v-8h-2Z"/>
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
                        {{ $glExpenses->links() }}
                    </div>
                @else
                    <p class="text-gray-600">{{ __('No GL expenses found.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

