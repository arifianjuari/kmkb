<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900"><?php echo e(__('Cost References')); ?></h2>
            <div class="flex items-center space-x-2">
                <form method="GET" action="<?php echo e(route('cost-references.index')); ?>" class="flex items-center space-x-2">
                    <div class="relative">
                        <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="<?php echo e(__('Search...')); ?>" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <?php echo e(__('Search')); ?>

                    </button>
                    <?php if($search): ?>
                        <a href="<?php echo e(route('cost-references.index')); ?>" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <?php echo e(__('Clear')); ?>

                        </a>
                    <?php endif; ?>
                </form>
                <a href="<?php echo e(route('cost-references.export')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <?php echo e(__('Export Excel')); ?>

                </a>
                <a href="<?php echo e(route('cost-references.create')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <?php echo e(__('Add New Cost Reference')); ?>

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
                        <p class="text-sm font-medium text-green-800">
                            <?php echo e(session('success')); ?>

                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <?php if($costReferences->count() > 0): ?>
                    <form id="bulk-delete-form" action="<?php echo e(route('cost-references.bulk-destroy')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>

                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm text-gray-600">
                                <?php echo e(__('Select records to delete in bulk')); ?>

                            </div>
                            <button id="bulk-delete-btn" type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed" disabled onclick="return confirm('<?php echo e(__('Are you sure you want to delete the selected cost references? This action cannot be undone.')); ?>')">
                                <?php echo e(__('Delete Selected')); ?>

                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <input id="select-all" type="checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Service Code')); ?></th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Description')); ?></th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Standard Cost (Rp)')); ?></th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Purchase Price (Rp)')); ?></th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Unit')); ?></th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Source')); ?></th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Actions')); ?></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php $__currentLoopData = $costReferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reference): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <input type="checkbox" name="ids[]" value="<?php echo e($reference->id); ?>" class="row-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($reference->service_code); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo e($reference->service_description); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right"><?php echo e(number_format($reference->standard_cost, 0, ',', '.')); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right"><?php echo e(number_format($reference->purchase_price, 0, ',', '.')); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($reference->unit); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($reference->source); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="<?php echo e(route('cost-references.show', $reference)); ?>" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    <?php echo e(__('View')); ?>

                                                </a>
                                                <a href="<?php echo e(route('cost-references.edit', $reference)); ?>" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ml-2">
                                                    <?php echo e(__('Edit')); ?>

                                                </a>
                                                <form action="<?php echo e(route('cost-references.destroy', $reference)); ?>" method="POST" class="inline ml-2">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('<?php echo e(__('Are you sure you want to delete this cost reference?')); ?>')">
                                                        <?php echo e(__('Delete')); ?>

                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                    
                    <div class="mt-6">
                        <?php echo e($costReferences->links()); ?>

                    </div>
                <?php else: ?>
                    <p class="text-gray-600"><?php echo e(__('No cost references found.')); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const bulkBtn = document.getElementById('bulk-delete-btn');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkForm = document.getElementById('bulk-delete-form');
        
        // Update button state based on checkbox selection
        function updateButtonState() {
            const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
            bulkBtn.disabled = !anyChecked;
        }
        
        // Select all functionality
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                rowCheckboxes.forEach(cb => { cb.checked = selectAll.checked; });
                updateButtonState();
            });
        }
        
        // Individual checkbox change
        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                if (selectAll) selectAll.checked = allChecked;
                updateButtonState();
            });
        });
        
        // Form submission with confirmation
        if (bulkForm) {
            bulkForm.addEventListener('submit', function(e) {
                const selectedCount = Array.from(rowCheckboxes).filter(cb => cb.checked).length;
                if (selectedCount === 0) {
                    e.preventDefault();
                    alert('Please select at least one item to delete.');
                    return false;
                }
                
                if (!confirm('Are you sure you want to delete the selected cost references? This action cannot be undone.')) {
                    e.preventDefault();
                    return false;
                }
            });
        }
        
        // Initialize state
        updateButtonState();
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/cost-references/index.blade.php ENDPATH**/ ?>