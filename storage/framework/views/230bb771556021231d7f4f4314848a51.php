<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900"><?php echo e(__('Expense Categories')); ?></h2>
            <div class="flex items-center space-x-2">
                <form method="GET" action="<?php echo e(route('expense-categories.index')); ?>" class="flex items-center space-x-2">
                    <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="<?php echo e(__('Search...')); ?>" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <select name="cost_type" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value=""><?php echo e(__('All Cost Types')); ?></option>
                        <option value="fixed" <?php echo e($costType == 'fixed' ? 'selected' : ''); ?>><?php echo e(__('Fixed')); ?></option>
                        <option value="variable" <?php echo e($costType == 'variable' ? 'selected' : ''); ?>><?php echo e(__('Variable')); ?></option>
                        <option value="semi_variable" <?php echo e($costType == 'semi_variable' ? 'selected' : ''); ?>><?php echo e(__('Semi Variable')); ?></option>
                    </select>
                    <select name="allocation_category" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value=""><?php echo e(__('All Categories')); ?></option>
                        <option value="gaji" <?php echo e($allocationCategory == 'gaji' ? 'selected' : ''); ?>><?php echo e(__('Gaji')); ?></option>
                        <option value="bhp_medis" <?php echo e($allocationCategory == 'bhp_medis' ? 'selected' : ''); ?>><?php echo e(__('BHP Medis')); ?></option>
                        <option value="bhp_non_medis" <?php echo e($allocationCategory == 'bhp_non_medis' ? 'selected' : ''); ?>><?php echo e(__('BHP Non Medis')); ?></option>
                        <option value="depresiasi" <?php echo e($allocationCategory == 'depresiasi' ? 'selected' : ''); ?>><?php echo e(__('Depresiasi')); ?></option>
                        <option value="lain_lain" <?php echo e($allocationCategory == 'lain_lain' ? 'selected' : ''); ?>><?php echo e(__('Lain-lain')); ?></option>
                    </select>
                    <select name="is_active" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value=""><?php echo e(__('All Status')); ?></option>
                        <option value="1" <?php echo e($isActive === '1' ? 'selected' : ''); ?>><?php echo e(__('Active')); ?></option>
                        <option value="0" <?php echo e($isActive === '0' ? 'selected' : ''); ?>><?php echo e(__('Inactive')); ?></option>
                    </select>
                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <?php echo e(__('Filter')); ?>

                    </button>
                    <?php if($search || $costType || $allocationCategory || $isActive !== null): ?>
                        <a href="<?php echo e(route('expense-categories.index')); ?>" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <?php echo e(__('Clear')); ?>

                        </a>
                    <?php endif; ?>
                </form>
                <a href="<?php echo e(route('expense-categories.export', request()->query())); ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    <?php echo e(__('Export Excel')); ?>

                </a>
                <a href="<?php echo e(route('expense-categories.create')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <?php echo e(__('Add New Expense Category')); ?>

                </a>
            </div>
        </div>
        
        <?php if(session('success')): ?>
            <div class="mb-6 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800"><?php echo e(session('success')); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="mb-6 rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800"><?php echo e(session('error')); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <?php if($expenseCategories->count() > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Account Code')); ?></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Account Name')); ?></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Cost Type')); ?></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Allocation Category')); ?></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Status')); ?></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__currentLoopData = $expenseCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($category->account_code); ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo e($category->account_name); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e(ucfirst(str_replace('_', ' ', $category->cost_type))); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e(ucfirst(str_replace('_', ' ', $category->allocation_category))); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                                <?php echo e($category->is_active ? __('Active') : __('Inactive')); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="<?php echo e(route('expense-categories.show', $category)); ?>" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                                <?php echo e(__('View')); ?>

                                            </a>
                                            <a href="<?php echo e(route('expense-categories.edit', $category)); ?>" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700 ml-2">
                                                <?php echo e(__('Edit')); ?>

                                            </a>
                                            <form action="<?php echo e(route('expense-categories.destroy', $category)); ?>" method="POST" class="inline ml-2">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700" onclick="return confirm('<?php echo e(__('Are you sure you want to delete this expense category?')); ?>')">
                                                    <?php echo e(__('Delete')); ?>

                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        <?php echo e($expenseCategories->links()); ?>

                    </div>
                <?php else: ?>
                    <p class="text-gray-600"><?php echo e(__('No expense categories found.')); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/expense-categories/index.blade.php ENDPATH**/ ?>