@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Edit Expense Category') }}</h2>
            <a href="{{ route('expense-categories.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('Back to List') }}
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Expense Category Details') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('expense-categories.update', $expenseCategory) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                        <div class="col-span-12 md:col-span-6">
                            <label for="account_code" class="block text-sm font-medium text-gray-700">{{ __('Account Code') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="text" id="account_code" name="account_code" value="{{ old('account_code', $expenseCategory->account_code) }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('account_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="cost_type" class="block text-sm font-medium text-gray-700">{{ __('Cost Type') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="cost_type" name="cost_type" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">{{ __('Select Cost Type') }}</option>
                                    <option value="fixed" {{ old('cost_type', $expenseCategory->cost_type) == 'fixed' ? 'selected' : '' }}>{{ __('Fixed') }}</option>
                                    <option value="variable" {{ old('cost_type', $expenseCategory->cost_type) == 'variable' ? 'selected' : '' }}>{{ __('Variable') }}</option>
                                    <option value="semi_variable" {{ old('cost_type', $expenseCategory->cost_type) == 'semi_variable' ? 'selected' : '' }}>{{ __('Semi Variable') }}</option>
                                </select>
                                @error('cost_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12">
                            <label for="account_name" class="block text-sm font-medium text-gray-700">{{ __('Account Name') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="text" id="account_name" name="account_name" value="{{ old('account_name', $expenseCategory->account_name) }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('account_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12">
                            <label for="allocation_category" class="block text-sm font-medium text-gray-700">{{ __('Allocation Category') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="allocation_category" name="allocation_category" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">{{ __('Select Allocation Category') }}</option>
                                    <option value="gaji" {{ old('allocation_category', $expenseCategory->allocation_category) == 'gaji' ? 'selected' : '' }}>{{ __('Gaji') }}</option>
                                    <option value="bhp_medis" {{ old('allocation_category', $expenseCategory->allocation_category) == 'bhp_medis' ? 'selected' : '' }}>{{ __('BHP Medis') }}</option>
                                    <option value="bhp_non_medis" {{ old('allocation_category', $expenseCategory->allocation_category) == 'bhp_non_medis' ? 'selected' : '' }}>{{ __('BHP Non Medis') }}</option>
                                    <option value="depresiasi" {{ old('allocation_category', $expenseCategory->allocation_category) == 'depresiasi' ? 'selected' : '' }}>{{ __('Depresiasi') }}</option>
                                    <option value="lain_lain" {{ old('allocation_category', $expenseCategory->allocation_category) == 'lain_lain' ? 'selected' : '' }}>{{ __('Lain-lain') }}</option>
                                </select>
                                @error('allocation_category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12">
                            <div class="flex items-center">
                                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $expenseCategory->is_active) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">{{ __('Active') }}</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Update Expense Category') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


