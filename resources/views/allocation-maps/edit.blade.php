@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Edit Allocation Map') }}</h2>
            <a href="{{ route('allocation-maps.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('Back to List') }}
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Allocation Map Details') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('allocation-maps.update', $allocationMap) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                        <div class="col-span-12 md:col-span-6">
                            <label for="source_cost_center_id" class="block text-sm font-medium text-gray-700">{{ __('Source Cost Center') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="source_cost_center_id" name="source_cost_center_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">{{ __('Select Source Cost Center') }}</option>
                                    @foreach($supportCostCenters as $costCenter)
                                        <option value="{{ $costCenter->id }}" {{ old('source_cost_center_id', $allocationMap->source_cost_center_id) == $costCenter->id ? 'selected' : '' }}>
                                            {{ $costCenter->code }} - {{ $costCenter->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('source_cost_center_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Cost center support yang akan dialokasikan</p>
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="allocation_driver_id" class="block text-sm font-medium text-gray-700">{{ __('Allocation Driver') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="allocation_driver_id" name="allocation_driver_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">{{ __('Select Allocation Driver') }}</option>
                                    @foreach($allocationDrivers as $driver)
                                        <option value="{{ $driver->id }}" {{ old('allocation_driver_id', $allocationMap->allocation_driver_id) == $driver->id ? 'selected' : '' }}>
                                            {{ $driver->name }} ({{ $driver->unit_measurement }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('allocation_driver_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Driver yang digunakan untuk menentukan proporsi alokasi</p>
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="step_sequence" class="block text-sm font-medium text-gray-700">{{ __('Step Sequence') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="number" id="step_sequence" name="step_sequence" value="{{ old('step_sequence', $allocationMap->step_sequence) }}" required min="1" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                @error('step_sequence')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Urutan alokasi (1, 2, 3, dst.). Step yang lebih kecil akan dijalankan terlebih dahulu.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                            {{ __('Update Allocation Map') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

