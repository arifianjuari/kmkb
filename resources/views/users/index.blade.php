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
                    <input type="text" id="name" name="name" value="{{ request('name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email') }}</label>
                    <input type="email" id="email" name="email" value="{{ request('email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Role') }}</label>
                    <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-biru-dongker-700 focus:ring-biru-dongker-700 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">{{ __('All Roles') }}</option>
                        <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>{{ __('Superadmin') }}</option>
                        <option value="hospital_admin" {{ request('role') == 'hospital_admin' ? 'selected' : '' }}>{{ __('Hospital Admin') }}</option>
                        <option value="finance_costing" {{ request('role') == 'finance_costing' ? 'selected' : '' }}>{{ __('Finance Costing') }}</option>
                        <option value="hr_payroll" {{ request('role') == 'hr_payroll' ? 'selected' : '' }}>{{ __('HR Payroll') }}</option>
                        <option value="facility_asset" {{ request('role') == 'facility_asset' ? 'selected' : '' }}>{{ __('Facility Asset') }}</option>
                        <option value="simrs_integration" {{ request('role') == 'simrs_integration' ? 'selected' : '' }}>{{ __('SIMRS Integration') }}</option>
                        <option value="support_unit" {{ request('role') == 'support_unit' ? 'selected' : '' }}>{{ __('Support Unit') }}</option>
                        <option value="clinical_unit" {{ request('role') == 'clinical_unit' ? 'selected' : '' }}>{{ __('Clinical Unit') }}</option>
                        <option value="medrec_claims" {{ request('role') == 'medrec_claims' ? 'selected' : '' }}>{{ __('Medrec Claims') }}</option>
                        <option value="pathway_team" {{ request('role') == 'pathway_team' ? 'selected' : '' }}>{{ __('Pathway Team') }}</option>
                        <option value="management_auditor" {{ request('role') == 'management_auditor' ? 'selected' : '' }}>{{ __('Management Auditor') }}</option>
                        <!-- Legacy roles -->
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }} (Legacy)</option>
                        <option value="mutu" {{ request('role') == 'mutu' ? 'selected' : '' }}>{{ __('Mutu') }} (Legacy)</option>
                        <option value="klaim" {{ request('role') == 'klaim' ? 'selected' : '' }}>{{ __('Klaim') }} (Legacy)</option>
                        <option value="manajemen" {{ request('role') == 'manajemen' ? 'selected' : '' }}>{{ __('Manajemen') }} (Legacy)</option>
                        <option value="observer" {{ request('role') == 'observer' ? 'selected' : '' }}>{{ __('Observer') }} (Legacy)</option>
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
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm">
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
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->department }}</td>
                                    @if(auth()->user()->isSuperadmin())
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->hospital ? $user->hospital->name : '-' }}</td>
                                    @endif
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            @php
                                                $canManage = auth()->user()->isSuperadmin() ||
                                                    (auth()->user()->hospital_id === $user->hospital_id && $user->role !== 'superadmin');
                                            @endphp
                                            <a href="{{ route('users.show', $user) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700" title="{{ __('View') }}" aria-label="{{ __('View') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                    <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                                                </svg>
                                            </a>
                                            @if($canManage)
                                                <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-biru-dongker-800 hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 dark:border-gray-600 dark:text-biru-dongker-600 dark:hover:bg-biru-dongker-900" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                                                    </svg>
                                                </a>
                                                @if($user->id != Auth::id())
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this user?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-gray-600 dark:text-red-400 dark:hover:bg-red-900" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                                <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1Zm-3 6h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 9Zm3 2v8h2v-8H9Zm4 0v8h2v-8h-2Z"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
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
