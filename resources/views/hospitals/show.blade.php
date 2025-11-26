@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Detail Rumah Sakit') }}</h2>
            <div class="space-x-2">
                <a href="{{ route('hospitals.index') }}" class="btn-secondary">
                    {{ __('Kembali') }}
                </a>
                <a href="{{ route('hospitals.edit', $hospital) }}" class="btn-primary">
                    {{ __('Edit') }}
                </a>
            </div>
        </div>
        
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-6">
                @if($hospital->logo_path)
                    <img src="{{ storage_url($hospital->logo_path) }}" alt="{{ $hospital->name }}" class="h-20 w-20 rounded-full mr-6">
                @else
                    <x-hospital-avatar name="{{ $hospital->name }}" color="{{ $hospital->theme_color }}" size="20" class="mr-6" />
                @endif
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $hospital->name }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('Kode: :code', ['code' => $hospital->code]) }}</p>
                    <div class="mt-2">
                        @if($hospital->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                                {{ __('Aktif') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100">
                                {{ __('Nonaktif') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-4 dark:text-white">{{ __('Informasi Umum') }}</h4>
                    <div class="space-y-4">
                        <div class="flex">
                            <div class="w-1/3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Alamat') }}</div>
                            <div class="w-2/3 text-sm text-gray-900 dark:text-gray-100">{{ $hospital->address ?? '-' }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-1/3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Kontak') }}</div>
                            <div class="w-2/3 text-sm text-gray-900 dark:text-gray-100">{{ $hospital->contact ?? '-' }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-1/3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Warna Tema') }}</div>
                            <div class="w-2/3 text-sm text-gray-900 dark:text-gray-100">
                                @if($hospital->theme_color)
                                    <span class="inline-flex items-center">
                                        <span class="w-4 h-4 rounded-full mr-2" style="background-color: {{ $hospital->theme_color }};"></span>
                                        {{ $hospital->theme_color }}
                                    </span>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-4 dark:text-white">{{ __('Statistik') }}</h4>
                    <div class="space-y-4">
                        <div class="flex">
                            <div class="w-1/2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jumlah Pengguna') }}</div>
                            <div class="w-1/2 text-sm text-gray-900 dark:text-gray-100">{{ $hospital->users()->count() }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-1/2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jumlah Pathway') }}</div>
                            <div class="w-1/2 text-sm text-gray-900 dark:text-gray-100">{{ $hospital->clinicalPathways()->count() }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-1/2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jumlah Kasus') }}</div>
                            <div class="w-1/2 text-sm text-gray-900 dark:text-gray-100">{{ $hospital->patientCases()->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4 dark:text-white">{{ __('Pengguna Terkait') }}</h4>
            @if($hospital->users()->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Nama') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Email') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Peran') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Departemen') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($hospital->users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($user->hasRole('admin'))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100">{{ __('Admin') }}</span>
                                        @elseif($user->hasRole('mutu'))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100">{{ __('Mutu') }}</span>
                                        @elseif($user->hasRole('klaim'))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">{{ __('Klaim') }}</span>
                                        @elseif($user->hasRole('manajemen'))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100">{{ __('Manajemen') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $user->department }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">{{ __('Belum ada pengguna terkait dengan rumah sakit ini.') }}</p>
            @endif
        </div>
    </div>
</section>
@endsection
