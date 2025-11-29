<?php $__env->startSection('content'); ?>
<?php
    $costTypeTabs = [
        'all' => __('All Cost Types'),
        'fixed' => __('Fixed'),
        'variable' => __('Variable'),
        'semi_variable' => __('Semi Variable'),
    ];
    
    $allocationCategoryTabs = [
        'all' => __('All Categories'),
        'gaji' => __('Gaji'),
        'bhp_medis' => __('BHP Medis'),
        'bhp_non_medis' => __('BHP Non Medis'),
        'depresiasi' => __('Depresiasi'),
        'lain_lain' => __('Lain-lain'),
    ];
?>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap"><?php echo e(__('Expense Categories')); ?></h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-indigo-600 border border-indigo-200 rounded-full w-5 h-5 flex items-center justify-center hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                    onclick="const p = document.getElementById('expense-categories-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="<?php echo e(__('What is Expense Category?')); ?>"
                    title="<?php echo e(__('What is Expense Category?')); ?>"
                >
                    i
                </button>
            </div>
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
                <?php if(!auth()->user()?->isObserver()): ?>
                <a href="<?php echo e(route('expense-categories.create')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <?php echo e(__('Add New Expense Category')); ?>

                </a>
                <?php endif; ?>
            </div>
        </div>
        <div id="expense-categories-help" class="mb-4 hidden text-xs text-gray-700 bg-indigo-50 border border-indigo-100 rounded-md p-3">
            <p class="mb-1">
                <span class="font-semibold">Expense Category</span> adalah pengelompokan akun biaya (beban) rumah sakit, misalnya gaji, BHP medis, BHP non medis, depresiasi, dan biaya lain-lain.
            </p>
            <p class="mb-2">
                Kode 4 digit dengan awalan angka <span class="font-mono">5</span> mengikuti struktur chart of accounts, di mana <span class="font-mono">5xxx</span> berarti akun biaya (expenses), dan dua digit berikutnya (mis. <span class="font-mono">51xx</span>, <span class="font-mono">52xx</span>) membedakan kelompok seperti gaji, BHP medis, BHP non medis, depresiasi, dan lain-lain.
            </p>
            <div>
                <p class="font-semibold mb-1">Cost type menjelaskan perilaku biaya terhadap volume layanan:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li><span class="font-semibold">Fixed Cost (FC)</span>: tidak berubah signifikan ketika volume pasien naik/turun (gaji tetap, depresiasi alat, sewa gedung)</li>
                    <li><span class="font-semibold">Variable Cost (VC)</span>: naik-turun sebanding dengan jumlah tindakan/pasien (BHP, obat, bahan sekali pakai, makan pasien)</li>
                    <li><span class="font-semibold">Semi Fixed Cost</span>: awalnya tetap, lalu loncat ke level baru setelah melewati batas volume tertentu (misal menambah 1 perawat ketika pasien &gt; 5)</li>
                </ul>
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
                <div class="mb-6 space-y-4">
                    
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider"><?php echo e(__('Cost Type')); ?></p>
                        <div class="flex flex-wrap items-center gap-2">
                            <?php $__currentLoopData = $costTypeTabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $isActive = ($key === 'all' && !$costType) || ($key === $costType);
                                    $urlParams = request()->except('cost_type', 'page');
                                    if ($key !== 'all') {
                                        $urlParams['cost_type'] = $key;
                                    }
                                    $tabUrl = route('expense-categories.index', $urlParams);
                                ?>
                                <a
                                    href="<?php echo e($tabUrl); ?>"
                                    class="inline-flex items-center gap-2 px-4 py-2 border rounded-full text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500 <?php echo e($isActive ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'); ?>"
                                >
                                    <span><?php echo e($label); ?></span>
                                    <span class="text-xs font-semibold <?php echo e($isActive ? 'text-white/80' : 'text-gray-500'); ?>">
                                        <?php echo e($costTypeCounts[$key] ?? 0); ?>

                                    </span>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    
                    
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider"><?php echo e(__('Allocation Category')); ?></p>
                        <div class="flex flex-wrap items-center gap-2">
                            <?php $__currentLoopData = $allocationCategoryTabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $isActive = ($key === 'all' && !$allocationCategory) || ($key === $allocationCategory);
                                    $urlParams = request()->except('allocation_category', 'page');
                                    if ($key !== 'all') {
                                        $urlParams['allocation_category'] = $key;
                                    }
                                    $tabUrl = route('expense-categories.index', $urlParams);
                                ?>
                                <a
                                    href="<?php echo e($tabUrl); ?>"
                                    class="inline-flex items-center gap-2 px-4 py-2 border rounded-full text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-500 <?php echo e($isActive ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'); ?>"
                                >
                                    <span><?php echo e($label); ?></span>
                                    <span class="text-xs font-semibold <?php echo e($isActive ? 'text-white/80' : 'text-gray-500'); ?>">
                                        <?php echo e($allocationCategoryCounts[$key] ?? 0); ?>

                                    </span>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
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
                                            <div class="flex items-center gap-2">
                                                <a href="<?php echo e(route('expense-categories.show', $category)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500" title="<?php echo e(__('View')); ?>" aria-label="<?php echo e(__('View')); ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                                                    </svg>
                                                </a>
                                                <?php if(!auth()->user()?->isObserver()): ?>
                                                <a href="<?php echo e(route('expense-categories.edit', $category)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500" title="<?php echo e(__('Edit')); ?>" aria-label="<?php echo e(__('Edit')); ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                                                    </svg>
                                                </a>
                                                <form action="<?php echo e(route('expense-categories.destroy', $category)); ?>" method="POST" class="inline" onsubmit="return confirm('<?php echo e(__('Are you sure you want to delete this expense category?')); ?>')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500" title="<?php echo e(__('Delete')); ?>" aria-label="<?php echo e(__('Delete')); ?>">
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