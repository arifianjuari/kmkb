@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('GL Expense Details') }}</h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('gl-expenses.edit', $glExpense) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('gl-expenses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('GL Expense Information') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Period') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $glExpense->period_month }}/{{ $glExpense->period_year }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Cost Center') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $glExpense->costCenter ? $glExpense->costCenter->name . ' (' . $glExpense->costCenter->code . ')' : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Expense Category') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $glExpense->expenseCategory ? $glExpense->expenseCategory->account_name . ' (' . $glExpense->expenseCategory->account_code . ')' : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Amount') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">Rp {{ number_format($glExpense->amount, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Created At') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $glExpense->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection










