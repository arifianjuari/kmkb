<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900"><?php echo e(__('Add New Service Volume')); ?></h2>
            <a href="<?php echo e(route('service-volumes.index')); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <?php echo e(__('Back to List')); ?>

            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900"><?php echo e(__('Service Volume Details')); ?></h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="<?php echo e(route('service-volumes.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                        <div class="col-span-12 md:col-span-6">
                            <label for="period_year" class="block text-sm font-medium text-gray-700"><?php echo e(__('Year')); ?> <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="period_year" name="period_year" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                        <option value="<?php echo e($y); ?>" <?php echo e(old('period_year', date('Y')) == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <?php $__errorArgs = ['period_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="period_month" class="block text-sm font-medium text-gray-700"><?php echo e(__('Month')); ?> <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="period_month" name="period_month" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value=""><?php echo e(__('Select Month')); ?></option>
                                    <?php for($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?php echo e($m); ?>" <?php echo e(old('period_month', date('n')) == $m ? 'selected' : ''); ?>><?php echo e(date('F', mktime(0, 0, 0, $m, 1))); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <?php $__errorArgs = ['period_month'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="cost_reference_id" class="block text-sm font-medium text-gray-700"><?php echo e(__('Service')); ?> <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="cost_reference_id" name="cost_reference_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value=""><?php echo e(__('Select Service')); ?></option>
                                    <?php $__currentLoopData = $costReferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($cr->id); ?>" <?php echo e(old('cost_reference_id') == $cr->id ? 'selected' : ''); ?>><?php echo e($cr->service_code); ?> - <?php echo e($cr->service_description); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['cost_reference_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="tariff_class_id" class="block text-sm font-medium text-gray-700"><?php echo e(__('Tariff Class')); ?></label>
                            <div class="mt-1">
                                <select id="tariff_class_id" name="tariff_class_id" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value=""><?php echo e(__('No Tariff Class')); ?></option>
                                    <?php $__currentLoopData = $tariffClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($tc->id); ?>" <?php echo e(old('tariff_class_id') == $tc->id ? 'selected' : ''); ?>><?php echo e($tc->name); ?> (<?php echo e($tc->code); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['tariff_class_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        
                        <div class="col-span-12 md:col-span-6">
                            <label for="total_quantity" class="block text-sm font-medium text-gray-700"><?php echo e(__('Total Quantity')); ?> <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="number" id="total_quantity" name="total_quantity" step="0.01" min="0" value="<?php echo e(old('total_quantity')); ?>" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <?php $__errorArgs = ['total_quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <?php echo e(__('Save Service Volume')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/service-volumes/create.blade.php ENDPATH**/ ?>