@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Daftar Rumah Sakit') }}</h2>
            <a href="{{ route('hospitals.create') }}" class="btn-primary">
                {{ __('Tambah Rumah Sakit') }}
            </a>
        </div>
        
        <div class="p-6">
            @if(session('status'))
                <div class="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-900">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400 dark:text-green-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('status') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Nama') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Kode') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Alamat') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Kontak') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Status') }}</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($hospitals as $hospital)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    <div class="flex items-center">
                                        @php $logoPath = $hospital->logo_path; @endphp
                                        @if($logoPath && Storage::disk('public')->exists($logoPath))
                                            <img src="{{ Storage::disk('public')->url($logoPath) }}" alt="{{ $hospital->name }}" class="h-8 w-8 rounded-full mr-3">
                                        @else
                                            <x-hospital-avatar name="{{ $hospital->name }}" color="{{ $hospital->theme_color }}" size="8" class="mr-3" />
                                        @endif
                                        <span>{{ $hospital->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $hospital->code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($hospital->address, 50) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $hospital->contact }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($hospital->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                                            {{ __('Aktif') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100">
                                            {{ __('Nonaktif') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('hospitals.show', $hospital) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">{{ __('Lihat') }}</a>
                                    <a href="{{ route('hospitals.edit', $hospital) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">{{ __('Edit') }}</a>
                                    <form action="{{ route('hospitals.destroy', $hospital) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('{{ __('Apakah Anda yakin ingin menghapus rumah sakit ini?') }}')">
                                            {{ __('Hapus') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Tidak ada data rumah sakit.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
