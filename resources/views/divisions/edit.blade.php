@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">{{ __('Edit Division') }}</h2>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('divisions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('divisions.update', $division) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="col-span-2 md:col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }} <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <input type="text" id="name" name="name" value="{{ old('name', $division->name) }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label for="code" class="block text-sm font-medium text-gray-700">{{ __('Code') }}</label>
                    <div class="mt-1">
                        <input type="text" id="code" name="code" value="{{ old('code', $division->code) }}" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label for="parent_id" class="block text-sm font-medium text-gray-700">{{ __('Parent Division') }}</label>
                    <div class="mt-1">
                        <select id="parent_id" name="parent_id" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">
                            <option value="">-- Tidak Ada Parent --</option>
                            @foreach($parentDivisions as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $division->parent_id) == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }} @if($parent->code)({{ $parent->code }})@endif
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                    <div class="mt-1">
                        <textarea id="description" name="description" rows="3" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700">{{ old('description', $division->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="col-span-2">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $division->is_active) ? 'checked' : '' }} class="focus:ring-biru-dongker-700 h-4 w-4 text-biru-dongker-800 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_active" class="font-medium text-gray-700">{{ __('Active') }}</label>
                            <p class="text-gray-500">{{ __('Inactive divisions will not be available for selection.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                    {{ __('Update') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
