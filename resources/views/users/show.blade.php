@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('User Details') }}</h2>
            <div class="space-x-2">
                <a href="{{ route('users.index') }}" class="btn-secondary">
                    {{ __('Back to List') }}
                </a>
                @can('update-users')
                @if(auth()->user()->isSuperadmin() || (auth()->user()->hospital_id === $user->hospital_id && $user->role !== 'superadmin'))
                    <a href="{{ route('users.edit', $user) }}" class="btn-primary">
                        {{ __('Edit') }}
                    </a>
                @endif
                @endcan
            </div>
        </div>
        
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h5 class="text-lg font-medium text-gray-900 mb-4 dark:text-white">{{ __('User Information') }}</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="space-y-4">
                        <div class="flex">
                            <div class="w-1/3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Name') }}</div>
                            <div class="w-2/3 text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-1/3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email') }}</div>
                            <div class="w-2/3 text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-1/3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Role') }}</div>
                            <div class="w-2/3 text-sm">
                                @php
                                    $roleLabels = [
                                        'superadmin' => ['label' => __('Superadmin'), 'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100'],
                                        'hospital_admin' => ['label' => __('Hospital Admin'), 'class' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100'],
                                        'finance_costing' => ['label' => __('Finance Costing'), 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100'],
                                        'hr_payroll' => ['label' => __('HR Payroll'), 'class' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-100'],
                                        'facility_asset' => ['label' => __('Facility Asset'), 'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100'],
                                        'simrs_integration' => ['label' => __('SIMRS Integration'), 'class' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-100'],
                                        'support_unit' => ['label' => __('Support Unit'), 'class' => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-100'],
                                        'clinical_unit' => ['label' => __('Clinical Unit'), 'class' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'],
                                        'medrec_claims' => ['label' => __('Medrec Claims'), 'class' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-100'],
                                        'pathway_team' => ['label' => __('Pathway Team'), 'class' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-100'],
                                        'management_auditor' => ['label' => __('Management Auditor'), 'class' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-100'],
                                        // Legacy roles
                                        'admin' => ['label' => __('Admin'), 'class' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100'],
                                        'mutu' => ['label' => __('Mutu'), 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100'],
                                        'klaim' => ['label' => __('Klaim'), 'class' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'],
                                        'manajemen' => ['label' => __('Manajemen'), 'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100'],
                                        'observer' => ['label' => __('Observer') . ' (' . __('Read-only') . ')', 'class' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-100'],
                                    ];
                                    $roleInfo = $roleLabels[$user->role] ?? ['label' => ucfirst($user->role), 'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleInfo['class'] }}">{{ $roleInfo['label'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="space-y-4">
                        <div class="flex">
                            <div class="w-1/3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Department') }}</div>
                            <div class="w-2/3 text-sm text-gray-900 dark:text-gray-100">{{ $user->department }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-1/3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Created At') }}</div>
                            <div class="w-2/3 text-sm text-gray-900 dark:text-gray-100">{{ $user->created_at->format('d M Y H:i') }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-1/3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Last Updated') }}</div>
                            <div class="w-2/3 text-sm text-gray-900 dark:text-gray-100">{{ $user->updated_at->format('d M Y H:i') }}</div>
                        </div>
                        @if(auth()->user()->isSuperadmin())
                        <div class="flex">
                            <div class="w-1/3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Hospital') }}</div>
                            <div class="w-2/3 text-sm text-gray-900 dark:text-gray-100">{{ $user->hospital ? $user->hospital->name : '-' }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-6 bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h5 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Recent Activity') }}</h5>
        </div>
        <div class="p-6">
            @if($user->auditLogs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Action') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Description') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('IP Address') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Timestamp') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($user->auditLogs->take(10) as $log)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $log->action }}</td>
                                    <td class="px-6 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $log->description }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $log->ip_address }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">{{ __('No recent activity found for this user.') }}</p>
            @endif
        </div>
    </div>
</section>
@endsection
