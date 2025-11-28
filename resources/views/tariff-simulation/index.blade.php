@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Tariff Simulation') }}</h2>
            <a href="{{ route('final-tariffs.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('View Final Tariffs') }}
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Simulate Tariff Calculation') }}</h3>
                <p class="mt-1 text-sm text-gray-500">Simulasi tariff berdasarkan unit cost dengan berbagai skenario margin</p>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('tariff-simulation.simulate') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                        <div class="col-span-12 md:col-span-6">
                            <label for="version_label" class="block text-sm font-medium text-gray-700">{{ __('Unit Cost Version') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="version_label" name="version_label" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                    <option value="">{{ __('Select Version') }}</option>
                                    @foreach($versions as $version)
                                        <option value="{{ $version }}" {{ $selectedVersion == $version ? 'selected' : '' }}>{{ $version }}</option>
                                    @endforeach
                                </select>
                                @error('version_label')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Pilih versi unit cost yang akan digunakan untuk simulasi</p>
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="global_margin" class="block text-sm font-medium text-gray-700">{{ __('Global Margin (%)') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="number" id="global_margin" name="global_margin" value="{{ old('global_margin', $globalMargin * 100) }}" step="0.01" min="0" max="1000" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                @error('global_margin')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Margin global untuk semua layanan (contoh: 20 untuk 20%)</p>
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="jasa_sarana" class="block text-sm font-medium text-gray-700">{{ __('Jasa Sarana') }}</label>
                            <div class="mt-1">
                                <input type="number" id="jasa_sarana" name="jasa_sarana" value="{{ old('jasa_sarana', $jasaSarana) }}" step="0.01" min="0" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                @error('jasa_sarana')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Komponen fasilitas (opsional)</p>
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="jasa_pelayanan" class="block text-sm font-medium text-gray-700">{{ __('Jasa Pelayanan') }}</label>
                            <div class="mt-1">
                                <input type="number" id="jasa_pelayanan" name="jasa_pelayanan" value="{{ old('jasa_pelayanan', $jasaPelayanan) }}" step="0.01" min="0" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                @error('jasa_pelayanan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Komponen profesional (opsional)</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                            {{ __('Run Simulation') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

