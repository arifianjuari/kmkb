@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Add New GL Expense') }}</h2>
            <a href="{{ route('gl-expenses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('Back to List') }}
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('GL Expense Details') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('gl-expenses.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                        <div class="col-span-12 md:col-span-6">
                            <label for="period_year" class="block text-sm font-medium text-gray-700">{{ __('Year') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="period_year" name="period_year" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" {{ old('period_year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                                @error('period_year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="period_month" class="block text-sm font-medium text-gray-700">{{ __('Month') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="period_month" name="period_month" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">{{ __('Select Month') }}</option>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ old('period_month', date('n')) == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endfor
                                </select>
                                @error('period_month')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="cost_center_id" class="block text-sm font-medium text-gray-700">{{ __('Cost Center') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="cost_center_id" name="cost_center_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">{{ __('Select Cost Center') }}</option>
                                    @foreach($costCenters as $cc)
                                        <option value="{{ $cc->id }}" {{ old('cost_center_id') == $cc->id ? 'selected' : '' }}>{{ $cc->name }} ({{ $cc->code }})</option>
                                    @endforeach
                                </select>
                                @error('cost_center_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="expense_category_id" class="block text-sm font-medium text-gray-700">{{ __('Expense Category') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="expense_category_id" name="expense_category_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">{{ __('Select Expense Category') }}</option>
                                    @foreach($expenseCategories as $ec)
                                        <option value="{{ $ec->id }}" {{ old('expense_category_id') == $ec->id ? 'selected' : '' }}>{{ $ec->account_name }} ({{ $ec->account_code }})</option>
                                    @endforeach
                                </select>
                                @error('expense_category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="amount" class="block text-sm font-medium text-gray-700">{{ __('Amount (Rp)') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="number" id="amount" name="amount" step="1000" min="0" value="{{ old('amount') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="transaction_date" class="block text-sm font-medium text-gray-700">{{ __('Transaction Date') }}</label>
                            <div class="mt-1">
                                <input type="date" id="transaction_date" name="transaction_date" value="{{ old('transaction_date') }}" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                @error('transaction_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="reference_number" class="block text-sm font-medium text-gray-700">{{ __('Reference/Invoice Number') }}</label>
                            <div class="mt-1">
                                <input type="text" id="reference_number" name="reference_number" value="{{ old('reference_number') }}" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700" placeholder="{{ __('e.g., INV-001, SPJ-001') }}">
                                @error('reference_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="funding_source" class="block text-sm font-medium text-gray-700">{{ __('Funding Source') }}</label>
                            <div class="mt-1">
                                <input type="text" id="funding_source" name="funding_source" value="{{ old('funding_source') }}" list="funding-sources-list" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700" placeholder="{{ __('e.g., APBD, BLUD, Hibah') }}">
                                <datalist id="funding-sources-list">
                                    @foreach($fundingSources as $source)
                                        <option value="{{ $source }}">
                                    @endforeach
                                </datalist>
                                @error('funding_source')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12">
                            <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                            <div class="mt-1">
                                <textarea id="description" name="description" rows="3" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700" placeholder="{{ __('Optional description for this expense') }}">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                            {{ __('Save GL Expense') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection










