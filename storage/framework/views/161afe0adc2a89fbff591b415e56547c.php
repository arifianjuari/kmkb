<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-900"><?php echo e(__('Welcome to KMKB')); ?></h1>
        </div>

        <div class="p-6">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900"><?php echo e(__('Kendali Mutu Kendali Biaya')); ?></h2>
                <p class="mt-1 text-gray-600"><?php echo e(__('Clinical Pathway Based Quality and Cost Control System')); ?></p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900"><?php echo e(__('About KMKB')); ?></h3>
                    <p class="mt-2 text-gray-700"><?php echo e(__('The KMKB system is designed to help healthcare institutions implement clinical pathways for standardized patient care while controlling costs through INA-CBGs (Indonesian Case Based Groups) methodology.')); ?></p>

                    <h3 class="mt-6 text-lg font-semibold text-gray-900"><?php echo e(__('Key Features')); ?></h3>
                    <ul class="mt-2 list-disc list-inside space-y-1 text-gray-700">
                        <li><?php echo e(__('Clinical Pathway Management')); ?></li>
                        <li><?php echo e(__('Patient Case Tracking')); ?></li>
                        <li><?php echo e(__('Cost Variance Analysis')); ?></li>
                        <li><?php echo e(__('Compliance Monitoring')); ?></li>
                        <li><?php echo e(__('Reporting and Analytics')); ?></li>
                        <li><?php echo e(__('Audit Trail')); ?></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900"><?php echo e(__('Get Started')); ?></h3>
                    <?php if(Route::has('login')): ?>
                        <div class="mt-3 flex flex-col sm:flex-row sm:items-center gap-3">
                            <?php if(auth()->guard()->check()): ?>
                                <a href="<?php echo e(url('/dashboard')); ?>" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <?php echo e(__('Dashboard')); ?>

                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(route('login')); ?>" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <?php echo e(__('Login')); ?>

                                </a>

                                <?php if(Route::has('register')): ?>
                                    <a href="<?php echo e(route('register')); ?>" class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-900 border border-gray-300 text-sm font-medium rounded-md shadow hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        <?php echo e(__('Register')); ?>

                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mt-6">
                        <h4 class="text-base font-semibold text-gray-900"><?php echo e(__('User Roles')); ?></h4>
                        <ul class="mt-2 space-y-1 text-gray-700">
                            <li><span class="font-semibold"><?php echo e(__('Admin')); ?></span>: <?php echo e(__('Full system access')); ?></li>
                            <li><span class="font-semibold"><?php echo e(__('Mutu')); ?></span>: <?php echo e(__('Pathway management')); ?></li>
                            <li><span class="font-semibold"><?php echo e(__('Klaim')); ?></span>: <?php echo e(__('Case management')); ?></li>
                            <li><span class="font-semibold"><?php echo e(__('Manajemen')); ?></span>: <?php echo e(__('Reporting and analytics')); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/welcome.blade.php ENDPATH**/ ?>