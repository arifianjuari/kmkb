@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Create User') }}</h2>
            <a href="{{ route('users.index') }}" class="btn-secondary">
                {{ __('Back to List') }}
            </a>
        </div>
        
        <div class="p-6">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Name') }}</label>
                    <div class="mt-1">
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email Address') }}</label>
                    <div class="mt-1">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Password') }}</label>
                    <div class="mt-1">
                        <input type="password" id="password" name="password" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Confirm Password') }}</label>
                    <div class="mt-1">
                        <input type="password" id="password_confirmation" name="password_confirmation" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Role') }}</label>
                    <div class="mt-1">
                        <select id="role" name="role" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('role') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">{{ __('Select Role') }}</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                            <option value="mutu" {{ old('role') == 'mutu' ? 'selected' : '' }}>{{ __('Mutu') }}</option>
                            <option value="klaim" {{ old('role') == 'klaim' ? 'selected' : '' }}>{{ __('Klaim') }}</option>
                            <option value="manajemen" {{ old('role') == 'manajemen' ? 'selected' : '' }}>{{ __('Manajemen') }}</option>
                            <option value="observer" {{ old('role') == 'observer' ? 'selected' : '' }}>{{ __('Observer') }} ({{ __('Read-only') }})</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Observer role has read-only access to all modules') }}</p>
                    </div>
                    @error('role')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="department" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Department') }}</label>
                    <div class="mt-1">
                        <input type="text" id="department" name="department" value="{{ old('department') }}" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('department') border-red-500 @enderror dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    @error('department')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                @if(auth()->user()->isSuperadmin())
                <div class="mb-6">
                    <label for="hospital_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Hospital') }}</label>
                    <div class="mt-1">
                        <select id="hospital_id" name="hospital_id" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">{{ __('Select Hospital') }}</option>
                            @foreach(App\Models\Hospital::all() as $hospital)
                                <option value="{{ $hospital->id }}" {{ old('hospital_id') == $hospital->id ? 'selected' : '' }}>
                                    {{ $hospital->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('hospital_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                @endif
                
                <div class="flex justify-end space-x-2">
                    <a href="{{ route('users.index') }}" class="btn-secondary">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn-primary">
                        {{ __('Create User') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
