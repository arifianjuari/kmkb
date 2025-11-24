<?php
    $user = auth()->user();
    $isSuperadmin = $user?->isSuperadmin();
    $isAdmin = $user?->hasRole(\App\Models\User::ROLE_ADMIN);
?>

<aside 
    x-data="{ 
        collapsed: false,
        openGroups: {},
        toggleGroup(group) {
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
    class="fixed left-0 top-0 h-screen bg-slate-800 text-white transition-all duration-300 ease-in-out overflow-hidden flex flex-col shadow-lg"
>
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between min-h-16 py-3 px-4 border-b border-slate-700 bg-slate-800">
        <?php
            $logoPath = hospital('logo_path');
            $isAbsoluteUrl = $logoPath && (Str::startsWith($logoPath, ['http://', 'https://']));
            $normalizedPath = $logoPath;
            if ($logoPath && (Str::startsWith($logoPath, '/storage/') || Str::startsWith($logoPath, 'storage/'))) {
                $normalizedPath = ltrim(Str::after($logoPath, '/storage/'), '/');
            }
        ?>
        <a href="<?php echo e(route('dashboard')); ?>" x-show="!collapsed" class="flex items-start space-x-3 min-w-0 flex-1">
            <?php if($isAbsoluteUrl || ($normalizedPath && Storage::disk('public')->exists($normalizedPath))): ?>
                <img src="<?php echo e($isAbsoluteUrl ? $logoPath : Storage::disk('public')->url($normalizedPath)); ?>" alt="<?php echo e(hospital('name')); ?>" class="h-8 w-auto flex-shrink-0 mt-0.5" />
            <?php else: ?>
                <?php if (isset($component)) { $__componentOriginal9abb5b9f9947fec1aec288b20ca02d30 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9abb5b9f9947fec1aec288b20ca02d30 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.hospital-avatar','data' => ['name' => ''.e(hospital('name')).'','color' => ''.e(hospital('theme_color')).'','size' => '8','class' => 'block flex-shrink-0 mt-0.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('hospital-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e(hospital('name')).'','color' => ''.e(hospital('theme_color')).'','size' => '8','class' => 'block flex-shrink-0 mt-0.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9abb5b9f9947fec1aec288b20ca02d30)): ?>
<?php $attributes = $__attributesOriginal9abb5b9f9947fec1aec288b20ca02d30; ?>
<?php unset($__attributesOriginal9abb5b9f9947fec1aec288b20ca02d30); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9abb5b9f9947fec1aec288b20ca02d30)): ?>
<?php $component = $__componentOriginal9abb5b9f9947fec1aec288b20ca02d30; ?>
<?php unset($__componentOriginal9abb5b9f9947fec1aec288b20ca02d30); ?>
<?php endif; ?>
            <?php endif; ?>
            <span class="text-lg font-semibold break-words min-w-0 leading-tight"><?php echo e(hospital('name') ?? config('app.name', 'Laravel')); ?></span>
        </a>
        <a href="<?php echo e(route('dashboard')); ?>" x-show="collapsed" class="flex items-center justify-center w-full">
            <?php if($isAbsoluteUrl || ($normalizedPath && Storage::disk('public')->exists($normalizedPath))): ?>
                <img src="<?php echo e($isAbsoluteUrl ? $logoPath : Storage::disk('public')->url($normalizedPath)); ?>" alt="<?php echo e(hospital('name')); ?>" class="h-8 w-auto" />
            <?php else: ?>
                <?php if (isset($component)) { $__componentOriginal9abb5b9f9947fec1aec288b20ca02d30 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9abb5b9f9947fec1aec288b20ca02d30 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.hospital-avatar','data' => ['name' => ''.e(hospital('name')).'','color' => ''.e(hospital('theme_color')).'','size' => '8','class' => 'block']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('hospital-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e(hospital('name')).'','color' => ''.e(hospital('theme_color')).'','size' => '8','class' => 'block']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9abb5b9f9947fec1aec288b20ca02d30)): ?>
<?php $attributes = $__attributesOriginal9abb5b9f9947fec1aec288b20ca02d30; ?>
<?php unset($__attributesOriginal9abb5b9f9947fec1aec288b20ca02d30); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9abb5b9f9947fec1aec288b20ca02d30)): ?>
<?php $component = $__componentOriginal9abb5b9f9947fec1aec288b20ca02d30; ?>
<?php unset($__componentOriginal9abb5b9f9947fec1aec288b20ca02d30); ?>
<?php endif; ?>
            <?php endif; ?>
        </a>
        <button @click="toggleCollapse()" class="p-1.5 rounded-md hover:bg-slate-700 transition-colors flex-shrink-0 ml-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path :class="collapsed ? 'hidden' : ''" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                <path :class="collapsed ? '' : 'hidden'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-2 bg-slate-800">
        <?php if(auth()->guard()->check()): ?>
            <!-- Dashboard -->
            <a href="<?php echo e(route('dashboard')); ?>" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg mb-1 transition-colors <?php echo e(request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span x-show="!collapsed" class="truncate">Dashboard</span>
            </a>

            <?php if(!$isSuperadmin): ?>
                <!-- Master Data Group -->
                <?php if($isAdmin || $user->hasRole('manajemen')): ?>
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
                            <a href="<?php echo e(route('cost-references.index')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('cost-references.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Cost References</span>
                            </a>
                            <a href="<?php echo e(route('jkn-cbg-codes.index')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('jkn-cbg-codes.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">JKN CBG Codes</span>
                            </a>
                            <div class="border-t border-slate-600 my-1"></div>
                            <a href="<?php echo e(route('cost-centers.index')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('cost-centers.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Cost Centers</span>
                            </a>
                            <a href="<?php echo e(route('expense-categories.index')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('expense-categories.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Expense Categories</span>
                            </a>
                            <a href="<?php echo e(route('allocation-drivers.index')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('allocation-drivers.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Allocation Drivers</span>
                            </a>
                            <a href="<?php echo e(route('tariff-classes.index')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('tariff-classes.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Tariff Classes</span>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- GL & Expenses Group -->
                <?php if($isAdmin || $user->hasRole('manajemen')): ?>
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
                            <a href="<?php echo e(route('gl-expenses.index')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('gl-expenses.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">GL Expenses</span>
                            </a>
                            <a href="<?php echo e(route('driver-statistics.index')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('driver-statistics.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Driver Statistics</span>
                            </a>
                            <a href="<?php echo e(route('service-volumes.index')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('service-volumes.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Service Volumes</span>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Cost Allocation Group -->
                <?php if($isAdmin || $user->hasRole('manajemen')): ?>
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
                <?php endif; ?>

                <!-- Unit Costing Group -->
                <?php if($isAdmin || $user->hasRole('manajemen')): ?>
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
                            <a href="<?php echo e(route('service-volumes.index')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('service-volumes.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
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
                <?php endif; ?>

                <!-- Tariff Management Group -->
                <?php if($isAdmin || $user->hasRole('manajemen')): ?>
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
                <?php endif; ?>

                <!-- Clinical Pathways -->
                <a href="<?php echo e(route('pathways.index')); ?>" 
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg mb-1 transition-colors <?php echo e(request()->routeIs('pathways.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white'); ?>">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span x-show="!collapsed" class="truncate">Pathways</span>
                </a>

                <!-- Patient Cases -->
                <a href="<?php echo e(route('cases.index')); ?>" 
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg mb-1 transition-colors <?php echo e(request()->routeIs('cases.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white'); ?>">
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
                        <a href="<?php echo e(route('reports.compliance')); ?>" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('reports.compliance') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                            <span class="truncate">Pathway Compliance</span>
                        </a>
                        <a href="<?php echo e(route('reports.cost-variance')); ?>" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('reports.cost-variance') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                            <span class="truncate">Case Variance</span>
                        </a>
                        <a href="<?php echo e(route('reports.pathway-performance')); ?>" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('reports.pathway-performance') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
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
                <?php if($isAdmin): ?>
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
                            <a href="<?php echo e(route('users.index')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('users.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Users</span>
                            </a>
                            <a href="<?php echo e(route('audit-logs.index')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('audit-logs.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
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
                <?php endif; ?>

                <!-- SIMRS Integration Group (Admin only) -->
                <?php if($isAdmin): ?>
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
                            <a href="<?php echo e(route('simrs.master-barang')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('simrs.master-barang') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Master Barang</span>
                            </a>
                            <a href="<?php echo e(route('simrs.tindakan-rawat-jalan')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('simrs.tindakan-rawat-jalan') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Tindakan Rawat Jalan</span>
                            </a>
                            <a href="<?php echo e(route('simrs.tindakan-rawat-inap')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('simrs.tindakan-rawat-inap') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Tindakan Rawat Inap</span>
                            </a>
                            <a href="<?php echo e(route('simrs.laboratorium')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('simrs.laboratorium') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Laboratorium</span>
                            </a>
                            <a href="<?php echo e(route('simrs.radiologi')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('simrs.radiologi') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Radiologi</span>
                            </a>
                            <a href="<?php echo e(route('simrs.operasi')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('simrs.operasi') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Operasi</span>
                            </a>
                            <a href="<?php echo e(route('simrs.kamar')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('simrs.kamar') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Kamar</span>
                            </a>
                            <a href="<?php echo e(route('simrs.sync')); ?>" 
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors <?php echo e(request()->routeIs('simrs.sync') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-slate-700 hover:text-white'); ?>">
                                <span class="truncate">Sync Management</span>
                            </a>
                            <div class="border-t border-slate-600 my-1"></div>
                            <span class="flex items-center px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                                <span class="truncate">Connection Settings</span>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- Superadmin Menu -->
                <a href="<?php echo e(route('hospitals.index')); ?>" 
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg mb-1 transition-colors <?php echo e(request()->routeIs('hospitals.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white'); ?>">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span x-show="!collapsed" class="truncate">Hospitals</span>
                </a>
                <a href="<?php echo e(route('users.index')); ?>" 
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg mb-1 transition-colors <?php echo e(request()->routeIs('users.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white'); ?>">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="!collapsed" class="truncate">Users</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </nav>

    <!-- Sidebar Footer -->
    <div class="border-t border-slate-700 p-4 bg-slate-800">
        <?php if(auth()->guard()->check()): ?>
            <div class="flex items-center space-x-3" x-show="!collapsed">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold text-sm">
                        <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-xs text-gray-400 truncate"><?php echo e(auth()->user()->email); ?></p>
                </div>
            </div>
            <div x-show="collapsed" class="flex justify-center">
                <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold text-sm">
                    <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                </div>
            </div>
        <?php endif; ?>
    </div>
</aside>

<?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/components/sidebar.blade.php ENDPATH**/ ?>