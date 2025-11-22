<?php $__env->startSection('content'); ?>
<div class="mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
        <h2 class="text-xl font-semibold text-gray-900"><?php echo e(__('Clinical Pathway Details')); ?></h2>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="<?php echo e(route('pathways.index')); ?>" class="btn btn-outline"><?php echo e(__('Back to List')); ?></a>
            <a href="<?php echo e(route('pathways.edit', $pathway)); ?>" class="btn btn-primary"><?php echo e(__('Edit')); ?></a>
            <?php if(auth()->guard()->check()): ?>
            <?php if(auth()->user()?->hasRole('mutu') || auth()->user()?->hasRole('admin')): ?>
                <a href="<?php echo e(route('pathways.builder', $pathway)); ?>" class="btn btn-warning"><?php echo e(__('Builder')); ?></a>
                <form action="<?php echo e(route('pathways.duplicate', $pathway)); ?>" method="POST" class="contents">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-secondary"><?php echo e(__('Duplicate')); ?></button>
                </form>
                <form action="<?php echo e(route('pathways.version', $pathway)); ?>" method="POST" class="flex items-center gap-2">
                    <?php echo csrf_field(); ?>
                    <label for="bump" class="sr-only"><?php echo e(__('Version bump')); ?></label>
                    <select id="bump" name="bump" class="py-2 px-2 min-w-[140px] border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-white text-gray-900">
                        <option value="patch"><?php echo e(__('Patch')); ?></option>
                        <option value="minor"><?php echo e(__('Minor')); ?></option>
                        <option value="major"><?php echo e(__('Major')); ?></option>
                    </select>
                    <button type="submit" class="btn btn-success"><?php echo e(__('New Version')); ?></button>
                </form>
                <a href="<?php echo e(route('pathways.export-docx', $pathway)); ?>" class="btn btn-primary"><?php echo e(__('Export DOCX')); ?></a>
                <a href="<?php echo e(route('pathways.export-pdf', $pathway)); ?>" class="btn btn-secondary"><?php echo e(__('Export PDF')); ?></a>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-base font-semibold text-gray-900"><?php echo e(__('Pathway Information')); ?></h5>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <table class="min-w-full">
                        <tr>
                            <th class="py-2 pr-4 text-left text-sm font-medium text-gray-500"><?php echo e(__('Name')); ?></th>
                            <td class="py-2 text-sm text-gray-900"><?php echo e($pathway->name); ?></td>
                        </tr>
                        <tr>
                            <th class="py-2 pr-4 text-left text-sm font-medium text-gray-500"><?php echo e(__('Diagnosis Code')); ?></th>
                            <td class="py-2 text-sm text-gray-900"><?php echo e($pathway->diagnosis_code); ?></td>
                        </tr>
                        <tr>
                            <th class="py-2 pr-4 text-left text-sm font-medium text-gray-500"><?php echo e(__('Version')); ?></th>
                            <td class="py-2 text-sm text-gray-900"><?php echo e($pathway->version); ?></td>
                        </tr>
                    </table>
                </div>
                <div>
                    <table class="min-w-full">
                        <tr>
                            <th class="py-2 pr-4 text-left text-sm font-medium text-gray-500"><?php echo e(__('Effective Date')); ?></th>
                            <td class="py-2 text-sm text-gray-900"><?php echo e($pathway->effective_date->format('d M Y')); ?></td>
                        </tr>
                        <tr>
                            <th class="py-2 pr-4 text-left text-sm font-medium text-gray-500"><?php echo e(__('Status')); ?></th>
                            <td class="py-2">
                                <?php if($pathway->status == 'active'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"><?php echo e(__('Active')); ?></span>
                                <?php elseif($pathway->status == 'draft'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800"><?php echo e(__('Draft')); ?></span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"><?php echo e(__('Inactive')); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="py-2 pr-4 text-left text-sm font-medium text-gray-500"><?php echo e(__('Created By')); ?></th>
                            <td class="py-2 text-sm text-gray-900"><?php echo e($pathway->creator->name); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-semibold text-gray-900"><?php echo e(__('Description')); ?></label>
                <p class="mt-1 text-gray-700"><?php echo e($pathway->description); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-base font-semibold text-gray-900"><?php echo e(__('Pathway Steps')); ?></h5>
        </div>
        <div class="p-6">
            <?php if($pathway->steps->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Day')); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Activity')); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Description')); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Criteria')); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Standard Cost')); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Full Standard Cost')); ?></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $pathway->steps->sortBy('step_order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($step->step_order); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <span><?php echo e($step->service_code); ?></span>
                                            <?php if(method_exists($step, 'isConditional') ? $step->isConditional() : (!empty(trim($step->criteria ?? '')))): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800"><?php echo e(__('Conditional')); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo e($step->description); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo e($step->criteria); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp<?php echo e(number_format($step->estimated_cost, 0, ',', '.')); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp<?php echo e(number_format(($step->estimated_cost ?? 0) * $step->quantity, 0, ',', '.')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot class="bg-gray-50 font-semibold">
                            <tr>
                                <td colspan="5" class="px-6 py-3 text-right text-sm text-gray-900"><?php echo e(__('Total Standard Cost')); ?>:</td>
                                <td class="px-6 py-3 text-sm text-gray-900">
                                    <?php
                                        $totalCost = $pathway->steps->sum(function($step) {
                                            return ($step->estimated_cost ?? 0) * $step->quantity;
                                        });
                                    ?>
                                    Rp<?php echo e(number_format($totalCost, 0, ',', '.')); ?>

                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-600"><?php echo e(__('No steps defined for this pathway yet.')); ?></p>
            <?php endif; ?>

            <?php if(auth()->guard()->check()): ?>
            <?php if(auth()->user()?->hasRole('mutu') || auth()->user()?->hasRole('admin')): ?>
                <a href="#" class="inline-flex items-center mt-4 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"><?php echo e(__('Add Step')); ?></a>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/pathways/show.blade.php ENDPATH**/ ?>