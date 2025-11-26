@php
    $user = auth()->user();
    $isSuperadmin = $user?->isSuperadmin();
    $isAdmin = $user?->hasRole(\App\Models\User::ROLE_ADMIN);
    
    // Determine which groups should be open based on active routes
    $openGroups = [];
    
    // Master Data group
    if (request()->routeIs('cost-references.*') || 
        request()->routeIs('jkn-cbg-codes.*') || 
        request()->routeIs('cost-centers.*') || 
        request()->routeIs('expense-categories.*') || 
        request()->routeIs('allocation-drivers.*') || 
        request()->routeIs('tariff-classes.*')) {
        $openGroups['master-data'] = true;
    }
    
    // GL & Expenses group
    if (request()->routeIs('gl-expenses.*') || 
        request()->routeIs('driver-statistics.*') || 
        request()->routeIs('service-volumes.*')) {
        $openGroups['gl-expenses'] = true;
    }
    
    // Allocation group
    if (request()->routeIs('allocation-maps.*') || 
        request()->routeIs('allocation-results.*')) {
        $openGroups['allocation'] = true;
    }
    
    // Unit Cost group
    if (request()->routeIs('unit-cost.*') || 
        request()->routeIs('unit-cost-results.*')) {
        $openGroups['unit-cost'] = true;
    }
    
    // Tariff group
    if (request()->routeIs('tariff.*') || 
        request()->routeIs('tariff-simulation.*') || 
        request()->routeIs('tariff-explorer.*')) {
        $openGroups['tariff'] = true;
    }
    
    // Reports group
    if (request()->routeIs('reports.*')) {
        $openGroups['reports'] = true;
    }
    
    // Admin group
    if (request()->routeIs('users.*') || 
        request()->routeIs('audit-logs.*') || 
        request()->routeIs('roles.*') || 
        request()->routeIs('api-tokens.*') || 
        request()->routeIs('settings.*')) {
        $openGroups['admin'] = true;
    }
    
    // SIMRS group
    if (request()->routeIs('simrs.*')) {
        $openGroups['simrs'] = true;
    }
    
    // Service Volume (Current) group
    if (request()->routeIs('svc-current.*')) {
        $openGroups['svcCurrent'] = true;
    }
@endphp

<aside 
    x-data="{ 
        collapsed: false,
        openGroups: @js($openGroups),
        initialOpenGroups: @js($openGroups),
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
                <!-- Master Data Group -->
                @if($isAdmin || $user->hasRole('manajemen'))
                    <div class="mb-1">
                        <button @click="toggleGroup('master-data')" 
                                class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span x-show="!collapsed" class="truncate">Master Data</span>
                            </div>
                            <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('master-data') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="!collapsed && isGroupOpen('master-data')" class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('cost-references.index') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('cost-references.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Cost References</span>
                            </a>
                            <a href="{{ route('jkn-cbg-codes.index') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('jkn-cbg-codes.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">JKN CBG Codes</span>
                            </a>
                            <div class="border-t border-slate-600 my-1"></div>
                            <a href="{{ route('cost-centers.index') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('cost-centers.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Cost Centers</span>
                            </a>
                            <a href="{{ route('expense-categories.index') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('expense-categories.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Expense Categories</span>
                            </a>
                            <a href="{{ route('allocation-drivers.index') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('allocation-drivers.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Allocation Drivers</span>
                            </a>
                            <a href="{{ route('tariff-classes.index') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('tariff-classes.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Tariff Classes</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- GL & Expenses Group -->
                @if($isAdmin || $user->hasRole('manajemen'))
                    <div class="mb-1">
                        <button @click="toggleGroup('gl-expenses')" 
                                class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-show="!collapsed" class="truncate">GL & Expenses</span>
                            </div>
                            <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('gl-expenses') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="!collapsed && isGroupOpen('gl-expenses')" class="ml-8 mt-1 space-y-1">
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
                        </div>
                    </div>
                @endif

                <!-- Cost Allocation Group -->
                @if($isAdmin || $user->hasRole('manajemen'))
                    <div class="mb-1">
                        <button @click="toggleGroup('allocation')" 
                                class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span x-show="!collapsed" class="truncate">Allocation</span>
                            </div>
                            <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('allocation') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="!collapsed && isGroupOpen('allocation')" class="ml-8 mt-1 space-y-1">
                            <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                                <span class="truncate">Allocation Maps</span>
                            </span>
                            <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                                <span class="truncate">Run Allocation</span>
                            </span>
                            <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                                <span class="truncate">Allocation Results</span>
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Unit Costing Group -->
                @if($isAdmin || $user->hasRole('manajemen'))
                    <div class="mb-1">
                        <button @click="toggleGroup('unit-cost')" 
                                class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span x-show="!collapsed" class="truncate">Unit Cost</span>
                            </div>
                            <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('unit-cost') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="!collapsed && isGroupOpen('unit-cost')" class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('service-volumes.index') }}" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('service-volumes.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                                <span class="truncate">Service Volumes</span>
                            </a>
                            <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                                <span class="truncate">Calculate Unit Cost</span>
                            </span>
                            <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                                <span class="truncate">Unit Cost Results</span>
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Tariff Management Group -->
                @if($isAdmin || $user->hasRole('manajemen'))
                    <div class="mb-1">
                        <button @click="toggleGroup('tariff')" 
                                class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-show="!collapsed" class="truncate">Tariff</span>
                            </div>
                            <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('tariff') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="!collapsed && isGroupOpen('tariff')" class="ml-8 mt-1 space-y-1">
                            <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                                <span class="truncate">Tariff Simulation</span>
                            </span>
                            <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                                <span class="truncate">Final Tariffs</span>
                            </span>
                            <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                                <span class="truncate">Tariff Explorer</span>
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Clinical Pathways -->
                <a href="{{ route('pathways.index') }}" 
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg mb-1 transition-colors {{ request()->routeIs('pathways.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span x-show="!collapsed" class="truncate">Pathways</span>
                </a>

                <!-- Patient Cases -->
                <a href="{{ route('cases.index') }}" 
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg mb-1 transition-colors {{ request()->routeIs('cases.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span x-show="!collapsed" class="truncate">Cases</span>
                </a>

                <!-- Reports Group -->
                <div class="mb-1">
                    <button @click="toggleGroup('reports')" 
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span x-show="!collapsed" class="truncate">Reports</span>
                        </div>
                        <svg x-show="!collapsed" class="w-4 h-4 transition-transform" :class="isGroupOpen('reports') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="!collapsed && isGroupOpen('reports')" class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('reports.compliance') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('reports.compliance') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                            <span class="truncate">Pathway Compliance</span>
                        </a>
                        <a href="{{ route('reports.cost-variance') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('reports.cost-variance') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                            <span class="truncate">Case Variance</span>
                        </a>
                        <a href="{{ route('reports.pathway-performance') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('reports.pathway-performance') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white' }}">
                            <span class="truncate">Pathway Performance</span>
                        </a>
                        <div class="border-t border-slate-600 my-1"></div>
                        <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                            <span class="truncate">Cost Center Performance</span>
                        </span>
                        <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                            <span class="truncate">Allocation Summary</span>
                        </span>
                        <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                            <span class="truncate">Unit Cost Summary</span>
                        </span>
                        <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                            <span class="truncate">Tariff Comparison</span>
                        </span>
                    </div>
                </div>

                <!-- System Administration Group (Admin only) -->
                @if($isAdmin)
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

                <!-- SIMRS Integration Group (Admin only) -->
                @if($isAdmin)
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

                <!-- Service Volume (Current) Group (Admin only) -->
                @if($isAdmin)
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

    <!-- Sidebar Footer -->
    <div class="border-t border-slate-700 p-4 bg-slate-800">
        @auth
            <div class="flex items-center space-x-3" x-show="!collapsed">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <div x-show="collapsed" class="flex justify-center">
                <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold text-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        @endauth
    </div>
</aside>

