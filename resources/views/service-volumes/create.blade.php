@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Add New Service Volume') }}</h2>
            <a href="{{ route('service-volumes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('Back to List') }}
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Service Volume Details') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('service-volumes.store') }}" method="POST">
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
                            <label for="cost_reference_id" class="block text-sm font-medium text-gray-700">{{ __('Service') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="cost_reference_id" name="cost_reference_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">{{ __('Select Service') }}</option>
                                    @foreach($costReferences as $cr)
                                        <option value="{{ $cr->id }}" {{ old('cost_reference_id') == $cr->id ? 'selected' : '' }}>{{ $cr->service_code }} - {{ $cr->service_description }}</option>
                                    @endforeach
                                </select>
                                @error('cost_reference_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="tariff_class_id" class="block text-sm font-medium text-gray-700">{{ __('Tariff Class') }}</label>
                            <div class="mt-1">
                                <select id="tariff_class_id" name="tariff_class_id" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">{{ __('No Tariff Class') }}</option>
                                    @foreach($tariffClasses as $tc)
                                        <option value="{{ $tc->id }}" {{ old('tariff_class_id') == $tc->id ? 'selected' : '' }}>{{ $tc->name }} ({{ $tc->code }})</option>
                                    @endforeach
                                </select>
                                @error('tariff_class_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="total_quantity" class="block text-sm font-medium text-gray-700">{{ __('Total Quantity') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="number" id="total_quantity" name="total_quantity" step="0.01" min="0" value="{{ old('total_quantity') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                @error('total_quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                            {{ __('Save Service Volume') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection







