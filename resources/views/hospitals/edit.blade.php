@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Edit Rumah Sakit') }}</h2>
        </div>
        
        <div class="p-6">
            @if (session('status'))
                <div class="mb-4 rounded-md bg-green-50 p-4 border border-green-200">
                    <p class="text-sm text-green-800">{{ session('status') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
                    <p class="text-sm font-medium text-red-800 mb-2">{{ __('Terjadi kesalahan saat menyimpan.') }}</p>
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('hospitals.update', $hospital) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nama Rumah Sakit') }} *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $hospital->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Kode Rumah Sakit') }} *</label>
                            <input type="text" name="code" id="code" value="{{ old('code', $hospital->code) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Logo') }}</label>
                            @php
                                $logoPath = $hospital->logo_path;
                                $isAbsoluteUrl = $logoPath && (Str::startsWith($logoPath, ['http://', 'https://']));
                                $normalizedPath = $logoPath;
                                if ($logoPath && (Str::startsWith($logoPath, '/storage/') || Str::startsWith($logoPath, 'storage/'))) {
                                    $normalizedPath = ltrim(Str::after($logoPath, '/storage/'), '/');
                                }
                            @endphp
                            @if($isAbsoluteUrl || ($normalizedPath && Storage::disk(uploads_disk())->exists($normalizedPath)))
                                <div class="mb-2">
                                    <img src="{{ $isAbsoluteUrl ? $logoPath : storage_url($normalizedPath) }}" alt="{{ $hospital->name }}" class="h-16 w-16 rounded-full">
                                </div>
                            @elseif($logoPath)
                                <div class="mb-2">
                                    <x-hospital-avatar name="{{ $hospital->name }}" color="{{ $hospital->theme_color }}" size="16" />
                                </div>
                            @endif
                            <input type="file" name="logo" id="logo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-100 dark:hover:file:bg-blue-800">
                            @error('logo')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <div class="mb-4">
                            <label for="theme_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Warna Tema') }}</label>
                            <input type="color" name="theme_color" id="theme_color" value="{{ old('theme_color', $hospital->theme_color ?? '#2563eb') }}" class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                            @error('theme_color')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Alamat') }}</label>
                            <textarea name="address" id="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('address', $hospital->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Kontak') }}</label>
                            <input type="text" name="contact" id="contact" value="{{ old('contact', $hospital->contact) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('contact')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <div class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $hospital->is_active) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-white">
                            {{ __('Aktif') }}
                        </label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('hospitals.index') }}" class="btn-secondary">
                        {{ __('Batal') }}
                    </a>
                    <button type="submit" class="btn-primary">
                        {{ __('Simpan Perubahan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
