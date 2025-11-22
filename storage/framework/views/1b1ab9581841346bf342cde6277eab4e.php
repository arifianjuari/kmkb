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
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
        <div class="min-h-screen">
            <header>
                <?php echo $__env->make('layouts.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <!-- Spacer to offset fixed navbar height -->
                <div class="h-16"></div>

                <!-- Page Heading -->
                <?php if(isset($header)): ?>
                    <div class="bg-white shadow dark:bg-gray-800">
                        <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            <?php echo e($header); ?>

                        </div>
                    </div>
                <?php endif; ?>
            </header>

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
            <main class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <?php if(isset($slot)): ?>
                    <?php echo e($slot); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('content'); ?>
                <?php endif; ?>
            </main>
            
            
            <?php echo $__env->yieldContent('scripts'); ?>
            <?php echo $__env->yieldPushContent('scripts'); ?>
        </div>
    </body>
</html>
<?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/layouts/app.blade.php ENDPATH**/ ?>