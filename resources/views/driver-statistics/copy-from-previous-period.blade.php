@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Copy Static Data from Previous Period') }}</h2>
            <a href="{{ route('driver-statistics.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('Back to List') }}
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Copy Static Driver Statistics') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('Copy data dari driver yang ditandai sebagai static (data yang jarang berubah seperti Luas Lantai, Jumlah TT, dll) dari periode sebelumnya ke periode target.') }}</p>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @if($staticDrivers->isEmpty())
                    <div class="rounded-md bg-yellow-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-800">Tidak ada static drivers yang ditemukan. Silakan tandai beberapa allocation driver sebagai static terlebih dahulu.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mb-4 rounded-md bg-blue-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800">
                                    <strong>Static Drivers yang akan di-copy:</strong>
                                    @foreach($staticDrivers as $driver)
                                        <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs mr-1 mt-1">{{ $driver->name }}</span>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('driver-statistics.copy-from-previous-period.process') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('Source Period (Dari)') }}</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label for="source_year" class="block text-sm font-medium text-gray-700">{{ __('Year') }} <span class="text-red-500">*</span></label>
                                        <div class="mt-1">
                                            <select id="source_year" name="source_year" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                                <option value="">{{ __('Select Year') }}</option>
                                                @foreach($availablePeriods->unique('year')->sortByDesc('year') as $period)
                                                    <option value="{{ $period['year'] }}" {{ old('source_year') == $period['year'] ? 'selected' : '' }}>{{ $period['year'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('source_year')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="source_month" class="block text-sm font-medium text-gray-700">{{ __('Month') }} <span class="text-red-500">*</span></label>
                                        <div class="mt-1">
                                            <select id="source_month" name="source_month" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                                <option value="">{{ __('Select Month') }}</option>
                                                @for($m = 1; $m <= 12; $m++)
                                                    <option value="{{ $m }}" {{ old('source_month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                                @endfor
                                            </select>
                                            @error('source_month')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('Target Period (Ke)') }}</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label for="target_year" class="block text-sm font-medium text-gray-700">{{ __('Year') }} <span class="text-red-500">*</span></label>
                                        <div class="mt-1">
                                            <select id="target_year" name="target_year" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                                    <option value="{{ $y }}" {{ old('target_year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                                @endfor
                                            </select>
                                            @error('target_year')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="target_month" class="block text-sm font-medium text-gray-700">{{ __('Month') }} <span class="text-red-500">*</span></label>
                                        <div class="mt-1">
                                            <select id="target_month" name="target_month" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                                <option value="">{{ __('Select Month') }}</option>
                                                @for($m = 1; $m <= 12; $m++)
                                                    <option value="{{ $m }}" {{ old('target_month', date('n')) == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                                @endfor
                                            </select>
                                            @error('target_month')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($availablePeriods->isEmpty())
                            <div class="mt-4 rounded-md bg-yellow-50 p-4">
                                <p class="text-sm text-yellow-800">Tidak ada periode sebelumnya yang tersedia. Silakan input driver statistics terlebih dahulu.</p>
                            </div>
                        @endif
                        
                        <div class="mt-6">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed" {{ $availablePeriods->isEmpty() ? 'disabled' : '' }}>
                                {{ __('Copy Static Data') }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

