<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Superadmin Dashboard')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Removed Welcome Section -->
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Left column: two small stat cards in 2 cols -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Total Hospitals -->
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4zm0 6l9 4 9-4" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Total Hospitals</p>
                                <p class="text-2xl font-bold"><?php echo e($totalHospitals); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Users -->
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Total Users</p>
                                <p class="text-2xl font-bold"><?php echo e($totalUsers); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right column: Users by Role full width -->
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0a2 2 0 01-2-2m6 2a2 2 0 002-2m-4-6a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-700 font-semibold">Users by Role</p>
                                <p class="text-gray-500 text-sm">Distribution across roles</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <?php $__empty_1 = true; $__currentLoopData = $usersByRole; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="px-3 py-2 bg-gray-50 rounded-md text-center min-w-[90px]">
                                <div class="text-xs text-gray-500 font-medium uppercase tracking-wide"><?php echo e(ucfirst($role)); ?></div>
                                <div class="text-lg font-bold text-gray-800 leading-none"><?php echo e($count); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-gray-500">No users found.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Lists -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Recent Hospitals -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Hospitals</h3>
                        <a href="<?php echo e(route('hospitals.index')); ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Manage</a>
                    </div>
                    <div class="divide-y">
                        <?php $__empty_1 = true; $__currentLoopData = $recentHospitals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hospital): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="py-3 flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-800"><?php echo e($hospital->name); ?></p>
                                    <p class="text-gray-500 text-sm">Created <?php echo e($hospital->created_at?->diffForHumans()); ?></p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-gray-500">No hospitals found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Users</h3>
                        <a href="<?php echo e(route('users.index')); ?>" class="text-purple-600 hover:text-purple-800 text-sm font-medium">Manage</a>
                    </div>
                    <div class="divide-y">
                        <?php $__empty_1 = true; $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="py-3 flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-800"><?php echo e($user->name); ?></p>
                                    <p class="text-gray-500 text-sm">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"><?php echo e(ucfirst($user->role)); ?></span>
                                        <span class="mx-1">•</span>
                                        <?php echo e($user->hospital?->name ?? 'No hospital'); ?>

                                    </p>
                                </div>
                                <div class="text-xs text-gray-400"><?php echo e($user->created_at?->diffForHumans()); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-gray-500">No users found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="<?php echo e(route('hospitals.index')); ?>" class="flex flex-col items-center justify-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors duration-300">
                        <div class="bg-indigo-500 p-3 rounded-full text-white mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Manage Hospitals</span>
                    </a>
                    <a href="<?php echo e(route('users.index')); ?>" class="flex flex-col items-center justify-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-300">
                        <div class="bg-purple-500 p-3 rounded-full text-white mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Manage Users</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/dashboard/superadmin.blade.php ENDPATH**/ ?>