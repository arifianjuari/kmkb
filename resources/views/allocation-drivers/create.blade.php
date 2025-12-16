@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Add New Allocation Driver') }}</h2>
            <a href="{{ route('allocation-drivers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('Back to List') }}
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Allocation Driver Details') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('allocation-drivers.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                        <div class="col-span-12 md:col-span-6">
                            <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="unit_of_measurement_id" class="block text-sm font-medium text-gray-700">{{ __('Unit Measurement') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                @if(isset($uoms) && $uoms->count() > 0)
                                    <select id="unit_of_measurement_id" name="unit_of_measurement_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                        <option value="">{{ __('Select Unit') }}</option>
                                        @foreach($uoms as $uom)
                                            <option value="{{ $uom->id }}" {{ old('unit_of_measurement_id') == $uom->id ? 'selected' : '' }}>
                                                {{ $uom->name }} ({{ $uom->symbol ?? $uom->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_of_measurement_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        <a href="{{ route('units-of-measurement.create') }}" class="text-biru-dongker-700 hover:underline" target="_blank">{{ __('+ Add new unit') }}</a>
                                    </p>
                                @else
                                    <input type="text" id="unit_measurement" name="unit_measurement" value="{{ old('unit_measurement') }}" required placeholder="e.g. m2, orang, kg" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    @error('unit_measurement')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-span-12">
                            <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                            <div class="mt-1">
                                <textarea id="description" name="description" rows="3" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 resize-y">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12">
                            <div class="flex items-center">
                                <input type="checkbox" id="is_static" name="is_static" value="1" {{ old('is_static') ? 'checked' : '' }} class="h-4 w-4 text-biru-dongker-800 border-gray-300 rounded focus:ring-biru-dongker-700">
                                <label for="is_static" class="ml-2 block text-sm text-gray-900">{{ __('Static Data') }} <span class="text-gray-500 text-xs">(Data yang jarang berubah, seperti Luas Lantai, Jumlah TT)</span></label>
                            </div>
                            @error('is_static')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                            {{ __('Save Allocation Driver') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection






