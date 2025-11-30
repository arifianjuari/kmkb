<?php $__env->startSection('content'); ?>
<section class="mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white"><?php echo e(__('Clinical Pathways')); ?></h2>
        <?php if(!auth()->user()?->isObserver()): ?>
        <a href="<?php echo e(route('pathways.create')); ?>" class="btn-primary"><?php echo e(__('Create New Pathway')); ?></a>
        <?php endif; ?>
    </div>
    
    <div class="bg-white shadow rounded-lg overflow-hidden dark:bg-gray-800">
        <div class="p-0">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between gap-3 flex-wrap">
                <form action="<?php echo e(route('pathways.index')); ?>" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                    <label for="q" class="sr-only"><?php echo e(__('Search')); ?></label>
                    <input id="q" name="q" type="text" value="<?php echo e($q ?? ''); ?>" placeholder="<?php echo e(__('Search pathways...')); ?>" class="w-full sm:w-80 py-2 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-biru-dongker-700 focus:border-biru-dongker-700 dark:bg-gray-800 dark:text-gray-200" />
                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700">
                        <?php echo e(__('Search')); ?>

                    </button>
                    <?php if(!empty($q)): ?>
                        <a href="<?php echo e(route('pathways.index')); ?>" class="inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <?php echo e(__('Reset')); ?>

                        </a>
                    <?php endif; ?>
                </form>
            </div>
            <?php if($pathways->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Name')); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Diagnosis Code')); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Version')); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Effective Date')); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Status')); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Full Standard Cost')); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Created By')); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <?php $__currentLoopData = $pathways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pathway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300"><?php echo e($pathway->name); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300"><?php echo e($pathway->diagnosis_code); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300"><?php echo e($pathway->version); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300"><?php echo e($pathway->effective_date->format('d M Y')); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if($pathway->status == 'active'): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"><?php echo e(__('Active')); ?></span>
                                            <?php elseif($pathway->status == 'draft'): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200"><?php echo e(__('Draft')); ?></span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"><?php echo e(__('Inactive')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">Rp<?php echo e(number_format($pathway->steps->sum(function($step) { return ($step->estimated_cost ?? 0) * $step->quantity; }), 0, ',', '.')); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300"><?php echo e($pathway->creator->name); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center gap-2">
                                                <a href="<?php echo e(route('pathways.show', $pathway)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="<?php echo e(__('View')); ?>" aria-label="<?php echo e(__('View')); ?>">
                                                    <!-- Eye icon -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                                                    </svg>
                                                </a>
                                                <?php if(!auth()->user()?->isObserver()): ?>
                                                <a href="<?php echo e(route('pathways.edit', $pathway)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-biru-dongker-800 hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="<?php echo e(__('Edit')); ?>" aria-label="<?php echo e(__('Edit')); ?>">
                                                    <!-- Pencil icon -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                                                    </svg>
                                                </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update-pathways')): ?>
                                                    <a href="<?php echo e(route('pathways.builder', $pathway)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-biru-dongker-600 hover:bg-biru-dongker-50 focus:outline-none focus:ring-2 focus:ring-biru-dongker-500" title="<?php echo e(__('Builder')); ?>" aria-label="<?php echo e(__('Builder')); ?>">
                                                        <!-- Wrench icon -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                            <path d="M22 19.59 19.59 22l-6.3-6.3a7.004 7.004 0 0 1-8.71-9.97L7 8l3-3L5.59 1.41A7.004 7.004 0 0 1 15.56 10.3L22 16.72v2.87Z"/>
                                                        </svg>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('export-pathways')): ?>
                                                    <a href="<?php echo e(route('pathways.export-pdf', $pathway)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500" title="<?php echo e(__('Export PDF')); ?>" aria-label="<?php echo e(__('Export PDF')); ?>">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM8 14h1.5a1.5 1.5 0 0 0 0-3H8v3zm5-3h-1v3h1a1.5 1.5 0 0 0 0-3zm-1-6v4h4"/>
                                                            <path d="M9.5 13H9v-1h.5a.5.5 0 0 1 0 1zm4 0h-.5v-1h.5a.5.5 0 0 1 0 1z"/>
                                                        </svg>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-pathways')): ?>
                                                <form action="<?php echo e(route('pathways.destroy', $pathway)); ?>" method="POST" onsubmit="return confirm('<?php echo e(__('Are you sure you want to delete this pathway?')); ?>')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500" title="<?php echo e(__('Delete')); ?>" aria-label="<?php echo e(__('Delete')); ?>">
                                                        <!-- Trash icon -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                            <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1Zm-3 6h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 9Zm3 2v8h2v-8H9Zm4 0v8h2v-8h-2Z"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                    </table>
                </div>

                <div class="px-6 py-3 dark:text-gray-300"><?php echo e($pathways->links()); ?></div>
            <?php else: ?>
                <p class="p-6 text-gray-600 dark:text-gray-400"><?php echo e(__('No clinical pathways found.')); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/pathways/index.blade.php ENDPATH**/ ?>