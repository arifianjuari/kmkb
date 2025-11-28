@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Run Allocation') }}</h2>
            <a href="{{ route('allocation-maps.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                {{ __('Back to Allocation Maps') }}
            </a>
        </div>
        
        @if(session('error'))
            <div class="mb-6 rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        @if(session('errors'))
                            <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                                @foreach(session('errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if(session('warnings'))
            <div class="mb-6 rounded-md bg-yellow-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-800">Peringatan:</p>
                        <ul class="mt-2 list-disc list-inside text-sm text-yellow-700">
                            @foreach(session('warnings') as $warning)
                                <li>{{ $warning }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Form Run Allocation -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Run Allocation') }}</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <form action="{{ route('allocation.run') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menjalankan allocation untuk periode ini? Hasil allocation sebelumnya untuk periode ini akan dihapus.')">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700">{{ __('Year') }} <span class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <select id="year" name="year" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                            <option value="{{ $y }}" {{ old('year', $year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                    @error('year')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div>
                                <label for="month" class="block text-sm font-medium text-gray-700">{{ __('Month') }} <span class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <select id="month" name="month" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                                        @for($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ old('month', $month) == $m ? 'selected' : '' }}>
                                                {{ str_pad($m, 2, '0', STR_PAD_LEFT) }} - {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('month')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                                {{ __('Run Allocation') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Allocation Configuration Preview -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Allocation Configuration') }}</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    @if(count($summary['allocation_maps']) > 0)
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Total Steps</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $summary['steps_count'] }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-500 mb-2">Allocation Steps:</p>
                                <div class="space-y-2">
                                    @foreach($summary['allocation_maps'] as $map)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-biru-dongker-300 text-biru-dongker-900">
                                                    Step {{ $map['step'] }}
                                                </span>
                                                <span class="text-sm text-gray-700">{{ $map['source'] }}</span>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $map['driver'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="pt-4 border-t border-gray-200">
                                <p class="text-xs text-gray-500">
                                    <strong>Catatan:</strong> Pastikan GL Expenses dan Driver Statistics sudah lengkap untuk periode yang dipilih sebelum menjalankan allocation.
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Tidak ada allocation maps yang dikonfigurasi. Silakan setup allocation maps terlebih dahulu.</p>
                        <a href="{{ route('allocation-maps.create') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                            {{ __('Create Allocation Map') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

