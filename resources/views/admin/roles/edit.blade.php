@extends('layouts.app')

@section('content')
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Edit Role: {{ $roleDisplayName }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Role: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{{ $roleName }}</code>
                    </p>
                </div>
                <a href="{{ route('admin.roles.index') }}" class="btn-secondary">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>

        @if(session('error'))
            <div class="mx-6 mt-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-800 dark:bg-red-900 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.roles.update', $roleName) }}" method="POST" id="roleForm">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                <!-- Warning Box -->
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg dark:bg-yellow-900 dark:border-yellow-700">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div class="text-sm text-yellow-800 dark:text-yellow-200">
                            <strong>Peringatan:</strong> Perubahan akan langsung disimpan ke file <code class="bg-yellow-100 dark:bg-yellow-800 px-1 rounded">config/permissions.php</code>. 
                            File backup otomatis dibuat sebelum perubahan disimpan. Pastikan Anda yakin dengan perubahan yang akan dilakukan.
                        </div>
                    </div>
                </div>

                <!-- Menus Section -->
                <div class="border border-gray-200 rounded-lg p-4 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Menus yang Bisa Diakses</h3>
                            <span class="ml-2 cursor-help" title="Menu yang akan muncul di sidebar untuk role ini. Pilih menu yang diperlukan untuk role ini.">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" onclick="selectAllMenus()" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded dark:bg-gray-700 dark:hover:bg-gray-600">
                                Select All
                            </button>
                            <button type="button" onclick="deselectAllMenus()" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded dark:bg-gray-700 dark:hover:bg-gray-600">
                                Deselect All
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Centang menu yang akan ditampilkan di sidebar untuk role ini. Menu yang tidak dicentang tidak akan terlihat oleh user dengan role ini.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($menus as $menuKey => $menu)
                            <label class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer">
                                <input type="checkbox" 
                                       name="menus[]" 
                                       value="{{ $menuKey }}"
                                       class="mt-1 h-4 w-4 text-biru-dongker-800 focus:ring-biru-dongker-700 border-gray-300 rounded"
                                       {{ in_array($menuKey, $role['menus'] ?? []) || (isset($role['menus'][0]) && $role['menus'][0] === '*') ? 'checked' : '' }}>
                                <div class="ml-3 flex-1">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ ucfirst(str_replace(['-', '_'], ' ', $menuKey)) }}
                                    </div>
                                    @if(isset($menu['permission']))
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Permission: <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">{{ $menu['permission'] }}</code>
                                        </div>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Permissions Section -->
                <div class="border border-gray-200 rounded-lg p-4 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Permissions</h3>
                            <span class="ml-2 cursor-help" title="Permission menentukan aksi yang bisa dilakukan user. Format: action-resource (contoh: view-dashboard, create-pathways, delete-cases)">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" onclick="selectAllPermissions()" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded dark:bg-gray-700 dark:hover:bg-gray-600">
                                Select All
                            </button>
                            <button type="button" onclick="deselectAllPermissions()" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded dark:bg-gray-700 dark:hover:bg-gray-600">
                                Deselect All
                            </button>
                            <input type="text" 
                                   id="permissionFilter" 
                                   placeholder="Filter permissions..." 
                                   class="text-xs px-2 py-1 border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                                   onkeyup="filterPermissions()">
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Centang permission yang diperlukan. Format: <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">action-resource</code> 
                        (contoh: <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">view-dashboard</code>, 
                        <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">create-pathways</code>, 
                        <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">delete-cases</code>).
                    </p>
                    <div class="max-h-96 overflow-y-auto border border-gray-200 rounded p-3 dark:border-gray-700">
                        <div id="permissionsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($allPermissions as $permission)
                                <label class="flex items-center p-2 border border-gray-200 rounded hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer permission-item"
                                       data-permission="{{ strtolower($permission) }}">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $permission }}"
                                           class="h-4 w-4 text-biru-dongker-800 focus:ring-biru-dongker-700 border-gray-300 rounded"
                                           {{ in_array($permission, $role['permissions'] ?? []) || (isset($role['permissions'][0]) && $role['permissions'][0] === '*') ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">
                                        <code class="text-xs">{{ $permission }}</code>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        <strong>Tip:</strong> Gunakan filter untuk mencari permission tertentu. Permission yang dicentang akan menentukan aksi yang bisa dilakukan user dengan role ini.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.roles.index') }}" class="btn-secondary">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" 
                            class="btn-primary"
                            onclick="return confirm('Apakah Anda yakin ingin menyimpan perubahan? Perubahan akan langsung disimpan ke config file.')">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
function selectAllMenus() {
    document.querySelectorAll('input[name="menus[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllMenus() {
    document.querySelectorAll('input[name="menus[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function selectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        if (checkbox.closest('.permission-item').style.display !== 'none') {
            checkbox.checked = true;
        }
    });
}

function deselectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function filterPermissions() {
    const filter = document.getElementById('permissionFilter').value.toLowerCase();
    document.querySelectorAll('.permission-item').forEach(item => {
        const permission = item.getAttribute('data-permission');
        if (permission.includes(filter)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>
@endsection

