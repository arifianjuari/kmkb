<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Laravel')); ?></title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900" 
          x-data="{ 
              sidebarOpen: false, 
              sidebarCollapsed: window.sidebarCollapsed || false
          }"
          x-init="
              // Sync window global state with Alpine state
              window.sidebarCollapsed = sidebarCollapsed;
          "
          @toggle-sidebar.window="sidebarOpen = !sidebarOpen"
          @sidebar-toggle.window="sidebarCollapsed = $event.detail.collapsed; window.sidebarCollapsed = $event.detail.collapsed">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <div class="hidden lg:block">
                <?php if (isset($component)) { $__componentOriginal2880b66d47486b4bfeaf519598a469d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2880b66d47486b4bfeaf519598a469d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $attributes = $__attributesOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $component = $__componentOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__componentOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
            </div>
            
            <!-- Mobile Sidebar Overlay - Only visible on mobile (hidden on desktop via CSS) -->
            <div
                 x-show="sidebarOpen"
                 x-cloak
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30"></div>

            <!-- Mobile Sidebar - Only visible on mobile (hidden on desktop via CSS) -->
            <div
                 x-show="sidebarOpen"
                 x-cloak
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="lg:hidden fixed inset-y-0 left-0 z-40">
                <?php if (isset($component)) { $__componentOriginal2880b66d47486b4bfeaf519598a469d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2880b66d47486b4bfeaf519598a469d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $attributes = $__attributesOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $component = $__componentOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__componentOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden" 
                 x-bind:class="sidebarCollapsed ? 'lg:ml-16' : 'lg:ml-64'"
                 x-bind:style="'transition: margin-left 0.3s ease-in-out;'">
                <!-- Top Navigation Bar -->
                <header>
                    <?php echo $__env->make('layouts.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </header>

                <!-- Main Content -->
                <main class="flex-1 overflow-y-auto bg-gray-50">
                    <!-- Page Heading -->
                    <?php if(isset($header)): ?>
                        <div class="bg-white shadow dark:bg-gray-800">
                            <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                <?php echo e($header); ?>

                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Flash Messages -->
                    <?php if(session('success')): ?>
                        <div class="mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                                <strong class="font-bold">Success!</strong>
                                <span class="block sm:inline"><?php echo e(session('success')); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(session('error')): ?>
                        <div class="mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <strong class="font-bold">Error!</strong>
                                <span class="block sm:inline"><?php echo e(session('error')); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Page Content -->
                    <div class="py-6 px-4 sm:px-6 lg:px-8">
                        <?php if(isset($slot)): ?>
                            <?php echo e($slot); ?>

                        <?php else: ?>
                            <?php echo $__env->yieldContent('content'); ?>
                        <?php endif; ?>
                    </div>
                </main>
            </div>
        </div>
        
        
        <?php echo $__env->yieldContent('scripts'); ?>
        <?php echo $__env->yieldPushContent('scripts'); ?>
    </body>
</html>
<?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/layouts/app.blade.php ENDPATH**/ ?>