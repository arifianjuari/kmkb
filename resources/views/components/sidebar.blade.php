@php
    $user = auth()->user();
    $isSuperadmin = $user?->isSuperadmin();
    $isAdmin = $user?->hasRole(\App\Models\User::ROLE_ADMIN);
    $isObserver = $user?->isObserver();
    
    // Determine which groups should be open based on active routes
    $openGroups = [];
    
    // Setup group
    if (request()->routeIs('setup.*') || 
        request()->routeIs('cost-references.*') || 
        request()->routeIs('jkn-cbg-codes.*') || 
        request()->routeIs('cost-centers.*') || 
        request()->routeIs('expense-categories.*') || 
        request()->routeIs('allocation-drivers.*') || 
        request()->routeIs('tariff-classes.*') ||
        request()->routeIs('simrs.*')) {
        $openGroups['setup'] = true;
    }
    
    // Setup sub-groups
    if (request()->routeIs('cost-centers.*') || 
        request()->routeIs('expense-categories.*') || 
        request()->routeIs('allocation-drivers.*') || 
        request()->routeIs('tariff-classes.*')) {
        $openGroups['setup-costing'] = true;
    }
    if (request()->routeIs('cost-references.*') || 
        request()->routeIs('setup.service-catalog.*')) {
        $openGroups['setup-service-catalog'] = true;
    }
    if (request()->routeIs('jkn-cbg-codes.*') || 
        request()->routeIs('setup.jkn-cbg-codes.*')) {
        $openGroups['setup-jkn'] = true;
    }
    if (request()->routeIs('simrs.*') || 
        request()->routeIs('setup.simrs-integration.*')) {
        $openGroups['setup-simrs'] = true;
    }
    
    // Data Input group
    if (request()->routeIs('data-input.*') || 
        request()->routeIs('gl-expenses.*') || 
        request()->routeIs('driver-statistics.*') || 
        request()->routeIs('service-volumes.*')) {
        $openGroups['data-input'] = true;
    }
    
    // Costing Process group
    if (request()->routeIs('costing-process.*') || 
        request()->routeIs('allocation-maps.*') || 
        request()->routeIs('allocation-results.*') ||
        request()->routeIs('allocation.run.*')) {
        $openGroups['costing-process'] = true;
    }
    
    // Costing Process sub-groups
    if (request()->routeIs('costing-process.pre-allocation-check.*')) {
        $openGroups['costing-process-pre-check'] = true;
    }
    if (request()->routeIs('costing-process.allocation.*') || 
        request()->routeIs('allocation-maps.*') || 
        request()->routeIs('allocation-results.*') ||
        request()->routeIs('allocation.run.*')) {
        $openGroups['costing-process-allocation'] = true;
    }
    if (request()->routeIs('costing-process.unit-cost.*')) {
        $openGroups['costing-process-unit-cost'] = true;
    }
    
    // Tariff Management group
    if (request()->routeIs('tariffs.*') || 
        request()->routeIs('tariff.*') || 
        request()->routeIs('tariff-simulation.*') || 
        request()->routeIs('tariff-explorer.*') ||
        request()->routeIs('final-tariffs.*')) {
        $openGroups['tariff'] = true;
    }
    
    // Analytics group
    if (request()->routeIs('analytics.*') || 
        request()->routeIs('reports.*')) {
        $openGroups['analytics'] = true;
    }
    
    // Pathways group
    if (request()->routeIs('pathways.*')) {
        $openGroups['pathways'] = true;
    }
    
    // Cases group
    if (request()->routeIs('cases.*')) {
        $openGroups['cases'] = true;
    }
    
    // Admin group
    if (request()->routeIs('users.*') || 
        request()->routeIs('audit-logs.*') || 
        request()->routeIs('roles.*') || 
        request()->routeIs('api-tokens.*') || 
        request()->routeIs('settings.*')) {
        $openGroups['admin'] = true;
    }
@endphp

<aside 
    x-data="{ 
        collapsed: (() => {
            // Load state from localStorage on initialization
            const savedState = localStorage.getItem('sidebarCollapsed');
            return savedState === 'true' ? true : false;
        })(),
        openGroups: @js($openGroups),
        initialOpenGroups: @js($openGroups),
        init() {
            // Set initial state from localStorage
            const savedState = localStorage.getItem('sidebarCollapsed');
            this.collapsed = savedState === 'true' ? true : false;
            // Store state globally for navbar access
            window.sidebarCollapsed = this.collapsed;
            // Dispatch event to parent to sync main content margin
            window.dispatchEvent(new CustomEvent('sidebar-toggle', { detail: { collapsed: this.collapsed } }));
        },
        toggleGroup(group) {
            // Don't allow closing if a menu item in this group is active (was initially open)
            if (this.openGroups[group] && this.initialOpenGroups[group]) {
                return; // Keep it open - menu item in this group is active
            }
            this.openGroups[group] = !this.openGroups[group];
        },
        isGroupOpen(group) {
            return this.openGroups[group] || false;
        },
        toggleCollapse() {
            this.collapsed = !this.collapsed;
            // Save state to localStorage
            localStorage.setItem('sidebarCollapsed', this.collapsed.toString());
            // Store state globally for navbar access
            window.sidebarCollapsed = this.collapsed;
            // Dispatch event to parent to sync main content margin
            window.dispatchEvent(new CustomEvent('sidebar-toggle', { detail: { collapsed: this.collapsed } }));
        }
    }"
    :class="collapsed ? 'w-16' : 'w-64'"
    class="fixed left-0 top-0 h-screen bg-slate-800 text-white transition-all duration-300 ease-in-out overflow-visible flex flex-col shadow-lg z-40"
>
    <!-- Expand Button - Outside Sidebar -->
    <button @click="toggleCollapse()" 
            x-show="collapsed"
            class="fixed top-4 bg-white/80 backdrop-blur-sm hover:bg-white/90 rounded-md transition-all duration-200 flex items-center justify-center shadow-lg border border-gray-200 z-50"
            style="left: 68px; width: 36px; height: 36px;"
            :title="'Expand Sidebar'">
        <svg class="w-5 h-5 text-gray-600 transition-all duration-200" 
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
        </svg>
    </button>

    <!-- Sidebar Header -->
    <div :class="collapsed ? 'px-2' : 'px-4'" class="flex items-center justify-between min-h-16 py-3 border-b border-slate-700 bg-slate-800 relative">
        @php
            $logoPath = hospital('logo_path');
            $isAbsoluteUrl = $logoPath && (Str::startsWith($logoPath, ['http://', 'https://']));
            $normalizedPath = $logoPath;
            if ($logoPath && (Str::startsWith($logoPath, '/storage/') || Str::startsWith($logoPath, 'storage/'))) {
                $normalizedPath = ltrim(Str::after($logoPath, '/storage/'), '/');
            }
        @endphp
        <a href="{{ route('dashboard') }}" x-show="!collapsed" class="flex items-start space-x-3 min-w-0 flex-1">
            @if($isAbsoluteUrl || ($normalizedPath && Storage::disk(uploads_disk())->exists($normalizedPath)))
                <img src="{{ $isAbsoluteUrl ? $logoPath : storage_url($normalizedPath) }}" alt="{{ hospital('name') }}" class="h-8 w-auto flex-shrink-0 mt-0.5" />
            @else
                <x-hospital-avatar name="{{ hospital('name') }}" color="{{ hospital('theme_color') }}" size="8" class="block flex-shrink-0 mt-0.5" />
            @endif
            <span class="text-lg font-semibold break-words min-w-0 leading-tight">{{ hospital('name') ?? config('app.name', 'Laravel') }}</span>
        </a>
        <a href="{{ route('dashboard') }}" x-show="collapsed" class="flex items-center justify-center w-full h-full min-w-0">
            @if($isAbsoluteUrl || ($normalizedPath && Storage::disk(uploads_disk())->exists($normalizedPath)))
                <img src="{{ $isAbsoluteUrl ? $logoPath : storage_url($normalizedPath) }}" alt="{{ hospital('name') }}" class="h-8 w-auto max-h-8 max-w-[3rem] object-contain" />
            @else
                <x-hospital-avatar name="{{ hospital('name') }}" color="{{ hospital('theme_color') }}" size="8" class="block flex-shrink-0" />
            @endif
        </a>
        <button @click="toggleCollapse()" 
                x-show="!collapsed"
                class="p-1.5 hover:bg-slate-700 ml-2 rounded-md transition-all duration-200 flex-shrink-0 relative z-10 flex items-center justify-center"
                :title="'Collapse Sidebar'">
            <svg class="w-5 h-5 transition-all duration-200" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-2 bg-slate-800">
        @auth
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg mb-1 transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span x-show="!collapsed" class="truncate">Dashboard</span>
            </a>

            @if(!$isSuperadmin)
                <!-- Setup Group -->
                @if($isAdmin || $user->hasRole('manajemen') || $isObserver)
                    <div class="mb-1">
                        <button @click="toggleGroup('setup')" 
                                class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span x-show="!collapsed" class="truncate">Setup</span>
                            </div>
                            <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('setup') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="!collapsed && isGroupOpen('setup')" class="ml-8 mt-1 space-y-1">
                            <!-- Costing Setup -->
                            <div>
                                <button @click="toggleGroup('setup-costing')" 
                                        class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg text-gray-400 hover:bg-slate-700 hover:text-white transition-colors">
                                    <span class="truncate">Costing Setup</span>
                                    <svg class="w-3 h-3 transition-transform" :class="isGroupOpen('setup-costing') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="isGroupOpen('setup-costing')" class="ml-4 mt-1 space-y-1">
                                    <a href="{{ route('cost-centers.index') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('cost-centers.*') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Cost Centers</span>
                                    </a>
                                    <a href="{{ route('expense-categories.index') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('expense-categories.*') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Expense Categories</span>
                                    </a>
                                    <a href="{{ route('allocation-drivers.index') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('allocation-drivers.*') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Allocation Drivers</span>
                                    </a>
                                    <a href="{{ route('tariff-classes.index') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('tariff-classes.*') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Tariff Classes</span>
                                    </a>
                                </div>
                            </div>
                            <!-- Service Catalog -->
                            <div>
                                <button @click="toggleGroup('setup-service-catalog')" 
                                        class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg text-gray-400 hover:bg-slate-700 hover:text-white transition-colors">
                                    <span class="truncate">Service Catalog</span>
                                    <svg class="w-3 h-3 transition-transform" :class="isGroupOpen('setup-service-catalog') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="isGroupOpen('setup-service-catalog')" class="ml-4 mt-1 space-y-1">
                                    <a href="{{ route('cost-references.index') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('cost-references.*') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Service Items</span>
                                    </a>
                                    <a href="{{ route('setup.service-catalog.simrs-linked') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('setup.service-catalog.simrs-linked') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">SIMRS-linked Items</span>
                                    </a>
                                    <a href="{{ route('setup.service-catalog.import-export') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('setup.service-catalog.import-export') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Import/Export</span>
                                    </a>
                                </div>
                            </div>
                            <!-- JKN / INA-CBG Codes -->
                            <div>
                                <button @click="toggleGroup('setup-jkn')" 
                                        class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg text-gray-400 hover:bg-slate-700 hover:text-white transition-colors">
                                    <span class="truncate">JKN / INA-CBG Codes</span>
                                    <svg class="w-3 h-3 transition-transform" :class="isGroupOpen('setup-jkn') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="isGroupOpen('setup-jkn')" class="ml-4 mt-1 space-y-1">
                                    <a href="{{ route('jkn-cbg-codes.index') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('jkn-cbg-codes.*') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">CBG List</span>
                                    </a>
                                    <a href="{{ route('setup.jkn-cbg-codes.base-tariff') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('setup.jkn-cbg-codes.base-tariff') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Base Tariff Reference</span>
                                    </a>
                                </div>
                            </div>
                            <!-- SIMRS Integration -->
                            <div>
                                <button @click="toggleGroup('setup-simrs')" 
                                        class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg text-gray-400 hover:bg-slate-700 hover:text-white transition-colors">
                                    <span class="truncate">SIMRS Integration</span>
                                    <svg class="w-3 h-3 transition-transform" :class="isGroupOpen('setup-simrs') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="isGroupOpen('setup-simrs')" class="ml-4 mt-1 space-y-1">
                                    <a href="{{ route('setup.simrs-integration.settings') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('setup.simrs-integration.settings') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Connection Settings</span>
                                    </a>
                                    <a href="{{ route('simrs.master-barang') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('simrs.*') && !request()->routeIs('simrs.sync') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Data Sources</span>
                                    </a>
                                    <a href="{{ route('simrs.sync') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('simrs.sync') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Sync Management</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Data Input Group -->
                @if($isAdmin || $user->hasRole('manajemen') || $isObserver)
                    <div class="mb-1">
                        <button @click="toggleGroup('data-input')" 
                                class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <span x-show="!collapsed" class="truncate">Data Input</span>
                            </div>
                            <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('data-input') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="!collapsed && isGroupOpen('data-input')" class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('gl-expenses.index') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('gl-expenses.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">GL Expenses</span>
                            </a>
                            <a href="{{ route('driver-statistics.index') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('driver-statistics.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Driver Statistics</span>
                            </a>
                            <a href="{{ route('service-volumes.index') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('service-volumes.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Service Volumes</span>
                            </a>
                            <a href="{{ route('data-input.import-center') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('data-input.import-center') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Import Center</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Costing Process Group -->
                @if($isAdmin || $user->hasRole('manajemen') || $isObserver)
                    <div class="mb-1">
                        <button @click="toggleGroup('costing-process')" 
                                class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span x-show="!collapsed" class="truncate">Costing Process</span>
                            </div>
                            <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('costing-process') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="!collapsed && isGroupOpen('costing-process')" class="ml-8 mt-1 space-y-1">
                            <!-- Pre-Allocation Check -->
                            <div>
                                <button @click="toggleGroup('costing-process-pre-check')" 
                                        class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg text-gray-400 hover:bg-slate-700 hover:text-white transition-colors">
                                    <span class="truncate">Pre-Allocation Check</span>
                                    <svg class="w-3 h-3 transition-transform" :class="isGroupOpen('costing-process-pre-check') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="isGroupOpen('costing-process-pre-check')" class="ml-4 mt-1 space-y-1">
                                    <a href="{{ route('costing-process.pre-allocation-check.gl-completeness') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('costing-process.pre-allocation-check.gl-completeness') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">GL Completeness</span>
                                    </a>
                                    <a href="{{ route('costing-process.pre-allocation-check.driver-completeness') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('costing-process.pre-allocation-check.driver-completeness') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Driver Completeness</span>
                                    </a>
                                    <a href="{{ route('costing-process.pre-allocation-check.service-volume-completeness') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('costing-process.pre-allocation-check.service-volume-completeness') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Service Volume Completeness</span>
                                    </a>
                                    <a href="{{ route('costing-process.pre-allocation-check.mapping-validation') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('costing-process.pre-allocation-check.mapping-validation') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Mapping Validation</span>
                                    </a>
                                </div>
                            </div>
                            <!-- Allocation Engine -->
                            <div>
                                <button @click="toggleGroup('costing-process-allocation')" 
                                        class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg text-gray-400 hover:bg-slate-700 hover:text-white transition-colors">
                                    <span class="truncate">Allocation Engine</span>
                                    <svg class="w-3 h-3 transition-transform" :class="isGroupOpen('costing-process-allocation') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="isGroupOpen('costing-process-allocation')" class="ml-4 mt-1 space-y-1">
                                    <a href="{{ route('allocation-maps.index') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('allocation-maps.*') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Allocation Maps</span>
                                    </a>
                                    <a href="{{ route('allocation.run.form') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('allocation.run.*') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Run Allocation</span>
                                    </a>
                                    <a href="{{ route('allocation-results.index') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('allocation-results.*') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Allocation Results</span>
                                    </a>
                                </div>
                            </div>
                            <!-- Unit Cost Engine -->
                            <div>
                                <button @click="toggleGroup('costing-process-unit-cost')" 
                                        class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg text-gray-400 hover:bg-slate-700 hover:text-white transition-colors">
                                    <span class="truncate">Unit Cost Engine</span>
                                    <svg class="w-3 h-3 transition-transform" :class="isGroupOpen('costing-process-unit-cost') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="isGroupOpen('costing-process-unit-cost')" class="ml-4 mt-1 space-y-1">
                                    <a href="{{ route('costing-process.unit-cost.calculate') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('costing-process.unit-cost.calculate') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Calculate Unit Cost</span>
                                    </a>
                                    <a href="{{ route('costing-process.unit-cost.results') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('costing-process.unit-cost.results') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Unit Cost Results</span>
                                    </a>
                                    <a href="{{ route('costing-process.unit-cost.compare') }}" 
                                       class="flex items-center px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('costing-process.unit-cost.compare') ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-slate-700 hover:text-white' }}">
                                        <span class="truncate">Compare Versions</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tariff Management Group -->
                @if($isAdmin || $user->hasRole('manajemen') || $isObserver)
                    <div class="mb-1">
                        <button @click="toggleGroup('tariff')" 
                                class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-show="!collapsed" class="truncate">Tariff Management</span>
                            </div>
                            <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('tariff') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="!collapsed && isGroupOpen('tariff')" class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('tariff-simulation.index') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('tariff-simulation.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Tariff Simulation</span>
                            </a>
                            <a href="{{ route('tariffs.structure') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('tariffs.structure') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Tariff Structure Setup</span>
                            </a>
                            <a href="{{ route('final-tariffs.index') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('final-tariffs.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Final Tariffs</span>
                            </a>
                            <a href="{{ route('tariff-explorer.index') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('tariff-explorer.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Tariff Explorer</span>
                            </a>
                            <a href="{{ route('tariffs.comparison') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('tariffs.comparison') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Tariff vs INA-CBG</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Clinical Pathways -->
                <div class="mb-1">
                    <button @click="toggleGroup('pathways')" 
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span x-show="!collapsed" class="truncate">Clinical Pathways</span>
                        </div>
                        <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('pathways') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="!collapsed && isGroupOpen('pathways')" class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('pathways.index') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('pathways.index') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                            <span class="truncate">Pathway Repository</span>
                        </a>
                        <span class="flex items-center px-3 py-2 text-sm text-gray-500">
                            <span class="truncate">Pathway Builder</span>
                        </span>
                        <span class="flex items-center px-3 py-2 text-sm text-gray-500">
                            <span class="truncate">Pathway Cost Summary</span>
                        </span>
                        <span class="flex items-center px-3 py-2 text-sm text-gray-500">
                            <span class="truncate">Pathway Approval</span>
                        </span>
                        <a href="{{ route('pathways.templates') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('pathways.templates') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                            <span class="truncate">Template Import/Export</span>
                        </a>
                    </div>
                </div>

                <!-- Patient Cases -->
                <div class="mb-1">
                    <button @click="toggleGroup('cases')" 
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span x-show="!collapsed" class="truncate">Patient Cases</span>
                        </div>
                        <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('cases') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="!collapsed && isGroupOpen('cases')" class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('cases.index') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('cases.index') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                            <span class="truncate">Case Registration</span>
                        </a>
                        <span class="flex items-center px-3 py-2 text-sm text-gray-500">
                            <span class="truncate">Case Details</span>
                        </span>
                        <span class="flex items-center px-3 py-2 text-sm text-gray-500">
                            <span class="truncate">Case Costing</span>
                        </span>
                        <span class="flex items-center px-3 py-2 text-sm text-gray-500">
                            <span class="truncate">Case Variance Analysis</span>
                        </span>
                    </div>
                </div>

                <!-- Analytics & Improvement Group -->
                <div class="mb-1">
                    <button @click="toggleGroup('analytics')" 
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span x-show="!collapsed" class="truncate">Analytics & Improvement</span>
                        </div>
                        <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('analytics') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="!collapsed && isGroupOpen('analytics')" class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('analytics.cost-center-performance') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('analytics.cost-center-performance') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                            <span class="truncate">Cost Center Performance</span>
                        </a>
                        <a href="{{ route('analytics.allocation-summary') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('analytics.allocation-summary') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                            <span class="truncate">Allocation Summary</span>
                        </a>
                        <a href="{{ route('analytics.unit-cost-summary') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('analytics.unit-cost-summary') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                            <span class="truncate">Unit Cost Summary</span>
                        </a>
                        <a href="{{ route('analytics.tariff-analytics') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('analytics.tariff-analytics') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                            <span class="truncate">Tariff Analytics</span>
                        </a>
                        <a href="{{ route('reports.compliance') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('reports.compliance') || request()->routeIs('analytics.pathway-compliance') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                            <span class="truncate">Pathway Compliance</span>
                        </a>
                        <a href="{{ route('reports.cost-variance') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('reports.cost-variance') || request()->routeIs('analytics.case-variance') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                            <span class="truncate">Case Variance</span>
                        </a>
                        <a href="{{ route('reports.pathway-performance') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('reports.pathway-performance') || request()->routeIs('analytics.los-analysis') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                            <span class="truncate">LOS Analysis</span>
                        </a>
                        <a href="{{ route('analytics.continuous-improvement') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('analytics.continuous-improvement') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                            <span class="truncate">Continuous Improvement</span>
                        </a>
                    </div>
                </div>

                <!-- System Administration Group (Admin and Observer) -->
                @if($isAdmin || $isObserver)
                    <div class="mb-1">
                        <button @click="toggleGroup('admin')" 
                                class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span x-show="!collapsed" class="truncate">Admin</span>
                            </div>
                            <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('admin') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="!collapsed && isGroupOpen('admin')" class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('users.index') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Users</span>
                            </a>
                            <a href="{{ route('audit-logs.index') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('audit-logs.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Audit Logs</span>
                            </a>
                            <div class="border-t border-slate-600 my-1"></div>
                            <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                                <span class="truncate">Roles & Permissions</span>
                            </span>
                            <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                                <span class="truncate">API Tokens</span>
                            </span>
                            <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                                <span class="truncate">System Settings</span>
                            </span>
                        </div>
                    </div>
                @endif

                <!-- SIMRS Integration Group (Admin and Observer) -->
                @if($isAdmin || $isObserver)
                    <div class="mb-1">
                        <button @click="toggleGroup('simrs')" 
                                class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                                </svg>
                                <span x-show="!collapsed" class="truncate">SIMRS</span>
                            </div>
                            <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('simrs') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="!collapsed && isGroupOpen('simrs')" class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('simrs.master-barang') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('simrs.master-barang') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Master Barang</span>
                            </a>
                            <a href="{{ route('simrs.tindakan-rawat-jalan') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('simrs.tindakan-rawat-jalan') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Tindakan Rawat Jalan</span>
                            </a>
                            <a href="{{ route('simrs.tindakan-rawat-inap') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('simrs.tindakan-rawat-inap') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Tindakan Rawat Inap</span>
                            </a>
                            <a href="{{ route('simrs.laboratorium') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('simrs.laboratorium') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Laboratorium</span>
                            </a>
                            <a href="{{ route('simrs.radiologi') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('simrs.radiologi') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Radiologi</span>
                            </a>
                            <a href="{{ route('simrs.operasi') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('simrs.operasi') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Operasi</span>
                            </a>
                            <a href="{{ route('simrs.kamar') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('simrs.kamar') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Kamar</span>
                            </a>
                            <a href="{{ route('simrs.sync') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('simrs.sync') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Sync Management</span>
                            </a>
                            <div class="border-t border-slate-600 my-1"></div>
                            <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                                <span class="truncate">Connection Settings</span>
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Service Volume (Current) Group (Admin and Observer) -->
                @if($isAdmin || $isObserver)
                    <div class="mb-1">
                        <button @click="toggleGroup('svcCurrent')"
                                class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v4m-4 4h8m-4-2v4m-4 4h12m-6-2v4" />
                                </svg>
                                <span x-show="!collapsed" class="truncate">Service Volume (Current)</span>
                            </div>
                            <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('svcCurrent') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="!collapsed && isGroupOpen('svcCurrent')" class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('svc-current.master-barang') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('svc-current.master-barang') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Master Barang</span>
                            </a>
                            <a href="{{ route('svc-current.tindakan-rawat-jalan') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('svc-current.tindakan-rawat-jalan') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Tindakan Rawat Jalan</span>
                            </a>
                            <a href="{{ route('svc-current.tindakan-rawat-inap') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('svc-current.tindakan-rawat-inap') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Tindakan Rawat Inap</span>
                            </a>
                            <a href="{{ route('svc-current.laboratorium') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('svc-current.laboratorium') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Laboratorium</span>
                            </a>
                            <a href="{{ route('svc-current.radiologi') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('svc-current.radiologi') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Radiologi</span>
                            </a>
                            <a href="{{ route('svc-current.operasi') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('svc-current.operasi') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Operasi</span>
                            </a>
                            <a href="{{ route('svc-current.kamar') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('svc-current.kamar') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Kamar</span>
                            </a>
                            <a href="{{ route('svc-current.sync') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('svc-current.sync') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Sync Management</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- References (Last Menu) -->
                @can('viewAny', \App\Models\Reference::class)
                    <a href="{{ route('references.index') }}" 
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-lg mb-1 transition-colors {{ request()->routeIs('references.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h12M4 17h8" />
                        </svg>
                        <span x-show="!collapsed" class="truncate">{{ __('Referensi') }}</span>
                    </a>
                @endcan
            @else
                <!-- Superadmin Menu -->
                <a href="{{ route('hospitals.index') }}" 
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg mb-1 transition-colors {{ request()->routeIs('hospitals.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span x-show="!collapsed" class="truncate">Hospitals</span>
                </a>
                <a href="{{ route('users.index') }}" 
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg mb-1 transition-colors {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="!collapsed" class="truncate">Users</span>
                </a>
            @endif
        @endauth
    </nav>
</aside>

