@extends('layouts.app')

@section('content')
<section class="mx-auto py-6 sm:px-6 lg:px-8">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Audit Logs') }}</h2>
            <form action="{{ route('audit-logs.clear') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger" onclick="return confirm('{{ __('Are you sure you want to clear all audit logs? This action cannot be undone.') }}')">
                    {{ __('Clear All Logs') }}
                </button>
            </form>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6 dark:bg-gray-800">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('audit-logs.index') }}">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('User') }}</label>
                            <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">{{ __('All Users') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="action" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Action') }}</label>
                            <select id="action" name="action" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">{{ __('All Actions') }}</option>
                                <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>{{ __('Login') }}</option>
                                <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>{{ __('Logout') }}</option>
                                <option value="register" {{ request('action') == 'register' ? 'selected' : '' }}>{{ __('Register') }}</option>
                                <option value="password_reset" {{ request('action') == 'password_reset' ? 'selected' : '' }}>{{ __('Password Reset') }}</option>
                                <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>{{ __('Create') }}</option>
                                <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>{{ __('Update') }}</option>
                                <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>{{ __('Delete') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date From') }}</label>
                            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date To') }}</label>
                            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="submit" class="btn-primary">
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('audit-logs.index') }}" class="btn-secondary">
                            {{ __('Clear') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
            <div class="px-4 py-5 sm:p-6">
                @if($auditLogs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('User') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Action') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Description') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('IP Address') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('User Agent') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Timestamp') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @foreach($auditLogs as $log)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            @if($log->user)
                                                {{ $log->user->name }}
                                            @else
                                                <span class="text-gray-500 dark:text-gray-400">{{ __('System') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($log->action == 'login')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">{{ __('Login') }}</span>
                                            @elseif($log->action == 'logout')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">{{ __('Logout') }}</span>
                                            @elseif($log->action == 'register')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100">{{ __('Register') }}</span>
                                            @elseif($log->action == 'password_reset')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100">{{ __('Password Reset') }}</span>
                                            @elseif($log->action == 'create')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100">{{ __('Create') }}</span>
                                            @elseif($log->action == 'update')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100">{{ __('Update') }}</span>
                                            @elseif($log->action == 'delete')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100">{{ __('Delete') }}</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">{{ $log->action }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $log->description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $log->ip_address }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($log->user_agent, 30) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('audit-logs.show', $log) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700" title="{{ __('View') }}" aria-label="{{ __('View') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('audit-logs.destroy', $log) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this log entry?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-gray-600 dark:text-red-400 dark:hover:bg-red-900" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                            <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1Zm-3 6h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 9Zm3 2v8h2v-8H9Zm4 0v8h2v-8h-2Z"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 dark:text-gray-300">
                        {{ $auditLogs->links() }}
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">{{ __('No audit logs found.') }}</p>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
