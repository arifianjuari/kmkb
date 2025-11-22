@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Users') }}</h2>
            <a href="{{ route('users.create') }}" class="btn-primary">
                {{ __('Create New User') }}
            </a>
        </div>
        
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Name') }}</label>
                    <input type="text" id="name" name="name" value="{{ request('name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email') }}</label>
                    <input type="email" id="email" name="email" value="{{ request('email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Role') }}</label>
                    <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">{{ __('All Roles') }}</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                        <option value="mutu" {{ request('role') == 'mutu' ? 'selected' : '' }}>{{ __('Mutu') }}</option>
                        <option value="klaim" {{ request('role') == 'klaim' ? 'selected' : '' }}>{{ __('Klaim') }}</option>
                        <option value="manajemen" {{ request('role') == 'manajemen' ? 'selected' : '' }}>{{ __('Manajemen') }}</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-2 md:col-span-3">
                    <button type="submit" class="btn-primary">
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('users.index') }}" class="btn-secondary">
                        {{ __('Clear') }}
                    </a>
                </div>
            </form>
        </div>
        
        <div class="p-6">
            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Name') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Email') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Role') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Department') }}</th>
                                @if(auth()->user()->isSuperadmin())
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Hospital') }}</th>
                                @endif
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Created At') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($user->role === 'admin')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100">{{ __('Admin') }}</span>
                                        @elseif($user->role === 'mutu')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100">{{ __('Mutu') }}</span>
                                        @elseif($user->role === 'klaim')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">{{ __('Klaim') }}</span>
                                        @elseif($user->role === 'manajemen')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100">{{ __('Manajemen') }}</span>
                                        @elseif($user->role === 'superadmin')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-100">{{ __('Superadmin') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->department }}</td>
                                    @if(auth()->user()->isSuperadmin())
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->hospital ? $user->hospital->name : '-' }}</td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        @php
                                            $canManage = auth()->user()->isSuperadmin() ||
                                                (auth()->user()->hospital_id === $user->hospital_id && $user->role !== 'superadmin');
                                        @endphp
                                        <a href="{{ route('users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">{{ __('View') }}</a>
                                        @if($canManage)
                                            <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">{{ __('Edit') }}</a>
                                            @if($user->id != Auth::id())
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('{{ __('Are you sure you want to delete this user?') }}')">{{ __('Delete') }}</button>
                                                </form>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">{{ __('No users found.') }}</p>
            @endif
        </div>
    </div>
</section>
@endsection
