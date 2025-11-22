<?php $__env->startSection('content'); ?>
<section class="mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e(__('Edit Clinical Pathway')); ?></h2>
            <a href="<?php echo e(route('pathways.index')); ?>" class="btn-secondary">
                <?php echo e(__('Back to List')); ?>

            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
            <div class="px-4 py-5 sm:p-6">
                <form action="<?php echo e(route('pathways.update', $pathway)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Name')); ?></label>
                        <div class="mt-1">
                            <input type="text" id="name" name="name" value="<?php echo e(old('name', $pathway->name)); ?>" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Description')); ?></label>
                        <div class="mt-1">
                            <textarea id="description" name="description" rows="3" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white"><?php echo e(old('description', $pathway->description)); ?></textarea>
                        </div>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label for="diagnosis_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Diagnosis Code')); ?></label>
                            <div class="mt-1">
                                <input type="text" id="diagnosis_code" name="diagnosis_code" value="<?php echo e(old('diagnosis_code', $pathway->diagnosis_code)); ?>" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['diagnosis_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <?php $__errorArgs = ['diagnosis_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div>
                            <label for="version" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Version')); ?></label>
                            <div class="mt-1">
                                <input type="text" id="version" name="version" value="<?php echo e(old('version', $pathway->version)); ?>" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['version'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <?php $__errorArgs = ['version'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2 mt-6">
                        <div>
                            <label for="effective_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Effective Date')); ?></label>
                            <div class="mt-1">
                                <input type="date" id="effective_date" name="effective_date" value="<?php echo e(old('effective_date', $pathway->effective_date->format('Y-m-d'))); ?>" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['effective_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <?php $__errorArgs = ['effective_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Status')); ?></label>
                            <div class="mt-1">
                                <select id="status" name="status" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value=""><?php echo e(__('Select Status')); ?></option>
                                    <option value="draft" <?php echo e(old('status', $pathway->status) == 'draft' ? 'selected' : ''); ?>><?php echo e(__('Draft')); ?></option>
                                    <option value="active" <?php echo e(old('status', $pathway->status) == 'active' ? 'selected' : ''); ?>><?php echo e(__('Active')); ?></option>
                                    <option value="inactive" <?php echo e(old('status', $pathway->status) == 'inactive' ? 'selected' : ''); ?>><?php echo e(__('Inactive')); ?></option>
                                </select>
                            </div>
                            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-2">
                        <a href="<?php echo e(route('pathways.index')); ?>" class="btn-secondary">
                            <?php echo e(__('Cancel')); ?>

                        </a>
                        <button type="submit" class="btn-primary">
                            <?php echo e(__('Update Pathway')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/pathways/edit.blade.php ENDPATH**/ ?>