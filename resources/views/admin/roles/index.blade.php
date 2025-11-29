@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Roles & Permissions Management') }}</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Kelola menu dan permission untuk setiap role. Perubahan akan disimpan ke config file.
                    </p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-4 rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-800 dark:bg-green-900 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-800 dark:bg-red-900 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif

        <div class="p-6">
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900 dark:border-blue-700">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-blue-800 dark:text-blue-200">
                        <strong>Petunjuk:</strong>
                        <ul class="mt-2 list-disc list-inside space-y-1">
                            <li>Klik <strong>Edit</strong> pada role untuk mengatur menu dan permission yang bisa diakses</li>
                            <li>Perubahan akan langsung disimpan ke file <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">config/permissions.php</code></li>
                            <li>File backup otomatis dibuat sebelum perubahan disimpan</li>
                            <li>Role <strong>Superadmin</strong> memiliki akses penuh dan tidak dapat diubah</li>
                            <li>Setelah menyimpan, config cache akan otomatis di-clear</li>
                        </ul>
                    </div>
                </div>
            </div>

            @if(count($roles) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Role Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Menus
                                    <span class="ml-1" title="Jumlah menu yang bisa diakses">
                                        <svg class="w-4 h-4 inline text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Permissions
                                    <span class="ml-1" title="Jumlah permission yang dimiliki">
                                        <svg class="w-4 h-4 inline text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($roles as $roleName => $roleConfig)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $roleDisplayNames[$roleName] ?? ucfirst(str_replace('_', ' ', $roleName)) }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $roleName }}
                                                </div>
                                            </div>
                                            @if($roleName === 'superadmin')
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200" title="Role ini memiliki akses penuh dan tidak dapat diubah">
                                                    Full Access
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        @if(isset($roleConfig['menus']) && is_array($roleConfig['menus']))
                                            @if(in_array('*', $roleConfig['menus']))
                                                <span class="text-green-600 dark:text-green-400 font-medium">All Menus</span>
                                            @else
                                                <span class="font-medium">{{ count($roleConfig['menus']) }}</span>
                                                <span class="text-gray-500 dark:text-gray-400"> menu(s)</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        @if(isset($roleConfig['permissions']) && is_array($roleConfig['permissions']))
                                            @if(in_array('*', $roleConfig['permissions']))
                                                <span class="text-green-600 dark:text-green-400 font-medium">All Permissions</span>
                                            @else
                                                <span class="font-medium">{{ count($roleConfig['permissions']) }}</span>
                                                <span class="text-gray-500 dark:text-gray-400"> permission(s)</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($roleName === 'superadmin')
                                            <span class="text-gray-400 dark:text-gray-500 cursor-not-allowed" title="Role Superadmin tidak dapat diubah">
                                                Edit
                                            </span>
                                        @else
                                            <a href="{{ route('admin.roles.edit', $roleName) }}" 
                                               class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-biru-dongker-800 bg-white hover:bg-biru-dongker-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700 dark:bg-gray-700 dark:border-gray-600 dark:text-biru-dongker-400 dark:hover:bg-gray-600"
                                               title="Edit menu dan permission untuk role ini">
                                                Edit
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">Tidak ada role yang ditemukan.</p>
            @endif
        </div>
    </div>
</section>
@endsection

