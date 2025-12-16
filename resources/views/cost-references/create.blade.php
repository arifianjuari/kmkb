@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Add New Cost Catalogue Item') }}</h2>
            <a href="{{ route('cost-references.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                {{ __('Back to List') }}
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Cost Catalogue Item Details') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('cost-references.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                        <div class="col-span-12 md:col-span-6">
                            <label for="service_code" class="block text-sm font-medium text-gray-700">{{ __('Service Code') }}</label>
                            <div class="mt-1">
                                <input type="text" id="service_code" name="service_code" value="{{ old('service_code') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                @error('service_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="unit_of_measurement_id" class="block text-sm font-medium text-gray-700">{{ __('Unit') }}</label>
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
                                    <input type="text" id="unit" name="unit" value="{{ old('unit') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    @error('unit')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-span-12">
                            <label for="service_description" class="block text-sm font-medium text-gray-700">{{ __('Service Description') }}</label>
                            <div class="mt-1">
                                <textarea id="service_description" name="service_description" rows="3" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 resize-y">{{ old('service_description') }}</textarea>
                                @error('service_description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="standard_cost" class="block text-sm font-medium text-gray-700">{{ __('Standard Cost (Rp)') }}</label>
                            <div class="mt-1">
                                <input type="number" id="standard_cost" name="standard_cost" step="1000" min="0" value="{{ old('standard_cost') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                @error('standard_cost')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="source" class="block text-sm font-medium text-gray-700">{{ __('Source') }}</label>
                            <div class="mt-1">
                                <select id="source" name="source" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">{{ __('Select Source') }}</option>
                                    <option value="internal" {{ old('source') == 'internal' ? 'selected' : '' }}>{{ __('Internal') }}</option>
                                    <option value="external" {{ old('source') == 'external' ? 'selected' : '' }}>{{ __('External') }}</option>
                                </select>
                                @error('source')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-6">
                            <label for="category" class="block text-sm font-medium text-gray-700">{{ __('Category (optional)') }}</label>
                            <div class="mt-1">
                                <select id="category" name="category" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">{{ __('Select Category') }}</option>
                                    <option value="barang" {{ old('category') == 'barang' ? 'selected' : '' }}>{{ __('Obat/BHP') }}</option>
                                    <option value="tindakan_rj" {{ old('category') == 'tindakan_rj' ? 'selected' : '' }}>{{ __('Tindakan Rawat Jalan') }}</option>
                                    <option value="tindakan_ri" {{ old('category') == 'tindakan_ri' ? 'selected' : '' }}>{{ __('Tindakan Rawat Inap') }}</option>
                                    <option value="laboratorium" {{ old('category') == 'laboratorium' ? 'selected' : '' }}>{{ __('Laboratorium') }}</option>
                                    <option value="radiologi" {{ old('category') == 'radiologi' ? 'selected' : '' }}>{{ __('Radiologi') }}</option>
                                    <option value="operasi" {{ old('category') == 'operasi' ? 'selected' : '' }}>{{ __('Operasi') }}</option>
                                    <option value="kamar" {{ old('category') == 'kamar' ? 'selected' : '' }}>{{ __('Kamar') }}</option>
                                </select>
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                            {{ __('Save Cost Catalogue Item') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
