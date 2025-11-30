@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Add New Cost Center') }}</h2>
            <a href="{{ route('cost-centers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('Back to List') }}
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Cost Center Details') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('cost-centers.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                        <div class="col-span-12 md:col-span-6">
                            <label for="building_name" class="block text-sm font-medium text-gray-700">{{ __('Building Name') }}</label>
                            <div class="mt-1">
                                <select id="building_name" name="building_name" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700" onchange="updateCode(this)">
                                    <option value="">{{ __('Select Building / Unit') }}</option>
                                    @if(isset($candidates))
                                        <optgroup label="Poliklinik (Rawat Jalan)">
                                            @foreach($candidates['poliklinik'] as $poli)
                                                <option value="{{ $poli->name }}" data-code="{{ $poli->id }}" {{ old('building_name') == $poli->name ? 'selected' : '' }}>{{ $poli->name }}</option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Bangsal (Rawat Inap)">
                                            @foreach($candidates['bangsal'] as $bangsal)
                                                <option value="{{ $bangsal->name }}" data-code="{{ $bangsal->id }}" {{ old('building_name') == $bangsal->name ? 'selected' : '' }}>{{ $bangsal->name }}</option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Departemen">
                                            @foreach($candidates['departemen'] as $dep)
                                                <option value="{{ $dep->name }}" data-code="{{ $dep->id }}" {{ old('building_name') == $dep->name ? 'selected' : '' }}>{{ $dep->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    <option value="custom" class="font-bold text-blue-600">-- Custom Building --</option>
                                </select>
                                <input type="text" id="custom_building_name" name="custom_building_name" value="{{ old('custom_building_name') }}" class="mt-2 py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 hidden" placeholder="Enter custom building name">
                                @error('building_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-6">
                            <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Division') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="name" name="name" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">{{ __('Select Division') }}</option>
                                    @if(isset($divisions))
                                        @foreach($divisions as $division)
                                            <option value="{{ $division->name }}" {{ old('name') == $division->name ? 'selected' : '' }}>{{ $division->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-6">
                            <label for="code" class="block text-sm font-medium text-gray-700">{{ __('Code') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="text" id="code" name="code" value="{{ old('code') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                @error('code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="type" class="block text-sm font-medium text-gray-700">{{ __('Type') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="type" name="type" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">{{ __('Select Type') }}</option>
                                    <option value="support" {{ old('type') == 'support' ? 'selected' : '' }}>{{ __('Support') }}</option>
                                    <option value="revenue" {{ old('type') == 'revenue' ? 'selected' : '' }}>{{ __('Revenue') }}</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="floor" class="block text-sm font-medium text-gray-700">{{ __('Floor') }}</label>
                            <div class="mt-1">
                                <input type="number" id="floor" name="floor" value="{{ old('floor') }}" min="0" max="255" placeholder="e.g. 1" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                @error('floor')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="tariff_class_id" class="block text-sm font-medium text-gray-700">{{ __('Class') }}</label>
                            <div class="mt-1">
                                <select id="tariff_class_id" name="tariff_class_id" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">{{ __('No Class') }}</option>
                                    @foreach($tariffClasses as $tariffClass)
                                        <option value="{{ $tariffClass->id }}" {{ old('tariff_class_id') == $tariffClass->id ? 'selected' : '' }}>{{ $tariffClass->name }} ({{ $tariffClass->code }})</option>
                                    @endforeach
                                </select>
                                @error('tariff_class_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12">
                            <label for="parent_id" class="block text-sm font-medium text-gray-700">{{ __('Parent Cost Center') }}</label>
                            <div class="mt-1">
                                <select id="parent_id" name="parent_id" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">{{ __('No Parent') }}</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }} ({{ $parent->code }})</option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-span-12">
                            <div class="flex items-center">
                                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-biru-dongker-800 border-gray-300 rounded focus:ring-biru-dongker-700">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">{{ __('Active') }}</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                            {{ __('Save Cost Center') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateCode(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const codeInput = document.getElementById('code');
        const nameInput = document.getElementById('name');
        const customBuildingInput = document.getElementById('custom_building_name');
        
        if (selectElement.value === 'custom') {
            customBuildingInput.classList.remove('hidden');
            // customBuildingInput.required = true; // Optional depending on validation
            selectElement.name = ''; 
            customBuildingInput.name = 'building_name';
            
            // Clear code but don't clear name as user might have typed something
            codeInput.value = '';
            codeInput.readOnly = false;
        } else {
            customBuildingInput.classList.add('hidden');
            // customBuildingInput.required = false;
            selectElement.name = 'building_name';
            customBuildingInput.name = 'custom_building_name'; // Reset
            
            const code = selectedOption.getAttribute('data-code');
            const name = selectedOption.value;
            
            if (code) {
                codeInput.value = code;
            }
            
            // Auto-fill name logic removed as per request
        }
    }
</script>
@endpush






