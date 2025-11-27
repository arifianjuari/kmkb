@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Edit Driver Statistic') }}</h2>
            <a href="{{ route('driver-statistics.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('Back to List') }}
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Driver Statistic Details') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('driver-statistics.update', $driverStatistic) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                        <div class="col-span-12 md:col-span-6">
                            <label for="period_year" class="block text-sm font-medium text-gray-700">{{ __('Year') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="period_year" name="period_year" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" {{ old('period_year', $driverStatistic->period_year) == $y ? 'selected' : '' }}>{{ $y }}</option>
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
                                <select id="period_month" name="period_month" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">{{ __('Select Month') }}</option>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ old('period_month', $driverStatistic->period_month) == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
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
                                <select id="cost_center_id" name="cost_center_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">{{ __('Select Cost Center') }}</option>
                                    @foreach($costCenters as $cc)
                                        <option value="{{ $cc->id }}" {{ old('cost_center_id', $driverStatistic->cost_center_id) == $cc->id ? 'selected' : '' }}>{{ $cc->name }} ({{ $cc->code }})</option>
                                    @endforeach
                                </select>
                                @error('cost_center_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="allocation_driver_id" class="block text-sm font-medium text-gray-700">{{ __('Allocation Driver') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="allocation_driver_id" name="allocation_driver_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">{{ __('Select Allocation Driver') }}</option>
                                    @foreach($allocationDrivers as $ad)
                                        <option value="{{ $ad->id }}" {{ old('allocation_driver_id', $driverStatistic->allocation_driver_id) == $ad->id ? 'selected' : '' }}>{{ $ad->name }} ({{ $ad->unit_measurement }})</option>
                                    @endforeach
                                </select>
                                @error('allocation_driver_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="value" class="block text-sm font-medium text-gray-700">{{ __('Value') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="number" id="value" name="value" step="0.0001" min="0" value="{{ old('value', $driverStatistic->value) }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('value')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Update Driver Statistic') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection



