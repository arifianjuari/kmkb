@extends('layouts.app')

@section('content')
<div class="mx-auto py-6 sm:px-6 lg:px-8">
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Audit Log Details') }}</h2>
            <a href="{{ route('audit-logs.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                {{ __('Back to List') }}
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">{{ __('User') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($auditLog->user)
                                        <a href="{{ route('users.show', $auditLog->user) }}" class="text-biru-dongker-800 hover:text-biru-dongker-950">{{ $auditLog->user->name }}</a>
                                    @else
                                        <span class="text-gray-500">{{ __('System') }}</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Action') }}</dt>
                                <dd class="mt-1 text-sm">
                                    @if($auditLog->action == 'login')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ __('Login') }}</span>
                                    @elseif($auditLog->action == 'logout')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ __('Logout') }}</span>
                                    @elseif($auditLog->action == 'register')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ __('Register') }}</span>
                                    @elseif($auditLog->action == 'password_reset')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ __('Password Reset') }}</span>
                                    @elseif($auditLog->action == 'create')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ __('Create') }}</span>
                                    @elseif($auditLog->action == 'update')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ __('Update') }}</span>
                                    @elseif($auditLog->action == 'delete')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ __('Delete') }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $auditLog->action }}</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Description') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->description }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">{{ __('IP Address') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->ip_address }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">{{ __('User Agent') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->user_agent }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Timestamp') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->created_at->format('d M Y H:i:s') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                
                @if($auditLog->metadata)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700"><strong>{{ __('Metadata') }}</strong></label>
                        <pre class="mt-1 bg-gray-100 p-3 rounded-md text-sm overflow-x-auto">{{ json_encode($auditLog->metadata, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
