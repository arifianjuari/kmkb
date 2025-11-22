<?php $__env->startSection('content'); ?>
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white"><?php echo e(__('Patient Cases')); ?></h2>
            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <a href="<?php echo e(route('cases.create')); ?>" class="btn-primary">
                    <?php echo e(__('Create New Case')); ?>

                </a>
                <a href="<?php echo e(route('cases.upload')); ?>" class="btn-success">
                    <?php echo e(__('Upload CSV')); ?>

                </a>
                <a href="<?php echo e(route('cases.template')); ?>" class="btn-success">
                    <?php echo e(__('Download Template')); ?>

                </a>
            </div>
        </div>
        
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <form method="GET" action="<?php echo e(route('cases.index')); ?>" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="medical_record_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('MRN')); ?></label>
                    <input type="text" id="medical_record_number" name="medical_record_number" value="<?php echo e(request('medical_record_number')); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div>
                    <label for="pathway_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Pathway')); ?></label>
                    <select id="pathway_id" name="pathway_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value=""><?php echo e(__('All Pathways')); ?></option>
                        <?php $__currentLoopData = $pathways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pathway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($pathway->id); ?>" <?php echo e(request('pathway_id') == $pathway->id ? 'selected' : ''); ?>>
                                <?php echo e($pathway->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                <div>
                    <label for="admission_date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Admission Date From')); ?></label>
                    <input type="date" id="admission_date_from" name="admission_date_from" value="<?php echo e(request('admission_date_from')); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div>
                    <label for="admission_date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Admission Date To')); ?></label>
                    <input type="date" id="admission_date_to" name="admission_date_to" value="<?php echo e(request('admission_date_to')); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div class="flex justify-end space-x-2 md:col-span-4">
                    <button type="submit" class="btn-primary">
                        <?php echo e(__('Filter')); ?>

                    </button>
                    <a href="<?php echo e(route('cases.index')); ?>" class="btn-secondary">
                        <?php echo e(__('Clear')); ?>

                    </a>
                </div>
            </form>
        </div>
        
        <div class="p-6">
            <?php if($cases->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('MRN')); ?></th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Patient Name')); ?></th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Pathway')); ?></th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Admission Date')); ?></th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Discharge Date')); ?></th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Compliance %')); ?></th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Cost Variance')); ?></th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <?php $__currentLoopData = $cases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"><?php echo e($case->medical_record_number); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"><?php echo e($case->patient_name); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"><?php echo e($case->clinicalPathway->name); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"><?php echo e($case->admission_date->format('d M Y')); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        <?php if($case->discharge_date): ?>
                                            <?php echo e($case->discharge_date->format('d M Y')); ?>

                                        <?php else: ?>
                                            <span class="text-gray-500 dark:text-gray-400"><?php echo e(__('Not Discharged')); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <?php if($case->compliance_percentage !== null): ?>
                                            <span class="<?php echo e($case->compliance_percentage >= 90 ? 'text-green-600 dark:text-green-400' : ($case->compliance_percentage >= 70 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400')); ?> font-semibold">
                                                <?php echo e(number_format($case->compliance_percentage, 2)); ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-500 dark:text-gray-400"><?php echo e(__('N/A')); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <?php if($case->cost_variance !== null): ?>
                                            <span class="<?php echo e($case->cost_variance <= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'); ?> font-semibold">
                                                Rp<?php echo e(number_format($case->cost_variance, 2)); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-500 dark:text-gray-400"><?php echo e(__('N/A')); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="<?php echo e(route('cases.show', $case)); ?>" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"><?php echo e(__('View')); ?></a>
                                        <a href="<?php echo e(route('cases.edit', $case)); ?>" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"><?php echo e(__('Edit')); ?></a>
                                        
                                        <form action="<?php echo e(route('cases.destroy', $case)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('<?php echo e(__('Are you sure you want to delete this case?')); ?>')"><?php echo e(__('Delete')); ?></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    <?php echo e($cases->links()); ?>

                </div>
            <?php else: ?>
                <p class="text-gray-500 dark:text-gray-400"><?php echo e(__('No patient cases found.')); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/cases/index.blade.php ENDPATH**/ ?>