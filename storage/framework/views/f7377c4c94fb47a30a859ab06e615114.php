<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900"><?php echo e(__('Add New Expense Category')); ?></h2>
            <a href="<?php echo e(route('expense-categories.index')); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <?php echo e(__('Back to List')); ?>

            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900"><?php echo e(__('Expense Category Details')); ?></h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form action="<?php echo e(route('expense-categories.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                        <div class="col-span-12 md:col-span-6">
                            <label for="account_code" class="block text-sm font-medium text-gray-700"><?php echo e(__('Account Code')); ?> <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="text" id="account_code" name="account_code" value="<?php echo e(old('account_code')); ?>" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <?php $__errorArgs = ['account_code'];
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
                            <label for="cost_type" class="block text-sm font-medium text-gray-700"><?php echo e(__('Cost Type')); ?> <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="cost_type" name="cost_type" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value=""><?php echo e(__('Select Cost Type')); ?></option>
                                    <option value="fixed" <?php echo e(old('cost_type') == 'fixed' ? 'selected' : ''); ?>><?php echo e(__('Fixed')); ?></option>
                                    <option value="variable" <?php echo e(old('cost_type') == 'variable' ? 'selected' : ''); ?>><?php echo e(__('Variable')); ?></option>
                                    <option value="semi_variable" <?php echo e(old('cost_type') == 'semi_variable' ? 'selected' : ''); ?>><?php echo e(__('Semi Variable')); ?></option>
                                </select>
                                <?php $__errorArgs = ['cost_type'];
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
                        
                        <div class="col-span-12">
                            <label for="account_name" class="block text-sm font-medium text-gray-700"><?php echo e(__('Account Name')); ?> <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="text" id="account_name" name="account_name" value="<?php echo e(old('account_name')); ?>" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <?php $__errorArgs = ['account_name'];
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
                        
                        <div class="col-span-12">
                            <label for="allocation_category" class="block text-sm font-medium text-gray-700"><?php echo e(__('Allocation Category')); ?> <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select id="allocation_category" name="allocation_category" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value=""><?php echo e(__('Select Allocation Category')); ?></option>
                                    <option value="gaji" <?php echo e(old('allocation_category') == 'gaji' ? 'selected' : ''); ?>><?php echo e(__('Gaji')); ?></option>
                                    <option value="bhp_medis" <?php echo e(old('allocation_category') == 'bhp_medis' ? 'selected' : ''); ?>><?php echo e(__('BHP Medis')); ?></option>
                                    <option value="bhp_non_medis" <?php echo e(old('allocation_category') == 'bhp_non_medis' ? 'selected' : ''); ?>><?php echo e(__('BHP Non Medis')); ?></option>
                                    <option value="depresiasi" <?php echo e(old('allocation_category') == 'depresiasi' ? 'selected' : ''); ?>><?php echo e(__('Depresiasi')); ?></option>
                                    <option value="lain_lain" <?php echo e(old('allocation_category') == 'lain_lain' ? 'selected' : ''); ?>><?php echo e(__('Lain-lain')); ?></option>
                                </select>
                                <?php $__errorArgs = ['allocation_category'];
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
                        
                        <div class="col-span-12">
                            <div class="flex items-center">
                                <input type="checkbox" id="is_active" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?> class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900"><?php echo e(__('Active')); ?></label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <?php echo e(__('Save Expense Category')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/expense-categories/create.blade.php ENDPATH**/ ?>