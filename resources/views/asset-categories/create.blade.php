@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Tambah Kategori Aset') }}</h2>
        <a href="{{ route('asset-categories.index') }}" class="btn-secondary">{{ __('Kembali') }}</a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('asset-categories.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Kode</label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Tipe <span class="text-red-500">*</span></label>
                        <select name="type" id="type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                            @foreach($typeOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="default_useful_life_years" class="block text-sm font-medium text-gray-700">Umur Ekonomis Default (Tahun) <span class="text-red-500">*</span></label>
                        <input type="number" name="default_useful_life_years" id="default_useful_life_years" value="{{ old('default_useful_life_years', 4) }}" min="1" max="100" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">
                        @error('default_useful_life_years')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm">{{ old('description') }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-biru-dongker-800 border-gray-300 rounded focus:ring-biru-dongker-700">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktif</label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn-primary">{{ __('Simpan') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
