<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap"><?php echo e(__('Service Volumes')); ?></h2>
                <button
                    type="button"
                    class="flex-shrink-0 text-xs font-semibold text-indigo-600 border border-indigo-200 rounded-full w-5 h-5 flex items-center justify-center hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                    onclick="const p = document.getElementById('service-volumes-help'); if (p) { p.classList.toggle('hidden'); }"
                    aria-label="<?php echo e(__('What is Service Volume?')); ?>"
                    title="<?php echo e(__('What is Service Volume?')); ?>"
                >
                    i
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <a href="<?php echo e(route('service-volumes.import')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <?php echo e(__('Import Excel')); ?>

                </a>
                <a href="<?php echo e(route('service-volumes.export', request()->query())); ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    <?php echo e(__('Export Excel')); ?>

                </a>
                <a href="<?php echo e(route('service-volumes.create')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <?php echo e(__('Add New Service Volume')); ?>

                </a>
            </div>
        </div>
        <div id="service-volumes-help" class="mb-4 hidden text-xs text-gray-700 bg-indigo-50 border border-indigo-100 rounded-md p-3">
            <p class="mb-2">
                <span class="font-semibold">Service Volume</span> adalah input volume output layanan per periode.
            </p>
            <div class="mb-2">
                <p class="font-semibold mb-1">Contoh:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>OK: jumlah operasi sektio sesarea, appendektomi, dll</li>
                    <li>Lab: jumlah pemeriksaan Darah Lengkap, GDS, dsb</li>
                    <li>RI: patient days / bed days</li>
                </ul>
            </div>
            <div class="mb-2">
                <p class="font-semibold mb-1">Peran di sistem:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Menjadi penyebut dalam perhitungan unit cost per layanan di cost center tersebut</li>
                    <li>Data ini dipakai lagi di modul Unit Cost (versi ringkas muncul di menu Unit Cost → Service Volumes)</li>
                </ul>
            </div>
            <div class="mt-3 pt-3 border-t border-indigo-200">
                <p class="font-semibold mb-1">Merupakan data mentah operasional (source data)</p>
                <p class="mb-2 ml-2">Ini adalah tempat menyimpan angka volume asli dari operasional RS untuk satu periode.</p>
                <div class="mb-2">
                    <p class="font-semibold mb-1">Ciri-cirinya:</p>
                    <ul class="list-disc list-inside space-y-1 ml-2">
                        <li>Berperan sebagai "data sumber" dari HIS / SIMRS / rekap manual</li>
                        <li>Isinya bisa komplet & kotor:
                            <ul class="list-circle list-inside ml-4 space-y-0.5">
                                <li>Semua jenis tindakan, termasuk yang nanti mungkin tidak kamu costing</li>
                                <li>Bisa termasuk kasus yang outlier, komplikasi, paket khusus, dsb</li>
                            </ul>
                        </li>
                        <li>Dipakai oleh:
                            <ul class="list-circle list-inside ml-4 space-y-0.5">
                                <li>Tim keuangan/akuntansi sebagai dasar rekonsiliasi dengan GL</li>
                                <li>Modul lain yang perlu total aktivitas rumah sakit secara keseluruhan</li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <p class="ml-2 italic text-gray-600">Analogi: ini seperti buku absen asli seluruh tindakan di rumah sakit per periode.</p>
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
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="<?php echo e(route('service-volumes.index')); ?>" class="grid grid-cols-1 gap-4 md:grid-cols-5">
                    <div>
                        <label for="period_year" class="block text-sm font-medium text-gray-700"><?php echo e(__('Year')); ?></label>
                        <select id="period_year" name="period_year" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                <option value="<?php echo e($y); ?>" <?php echo e($periodYear == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label for="period_month" class="block text-sm font-medium text-gray-700"><?php echo e(__('Month')); ?></label>
                        <select id="period_month" name="period_month" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value=""><?php echo e(__('All Months')); ?></option>
                            <?php for($m = 1; $m <= 12; $m++): ?>
                                <option value="<?php echo e($m); ?>" <?php echo e($periodMonth == $m ? 'selected' : ''); ?>><?php echo e(date('F', mktime(0, 0, 0, $m, 1))); ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label for="cost_reference_id" class="block text-sm font-medium text-gray-700"><?php echo e(__('Service')); ?></label>
                        <select id="cost_reference_id" name="cost_reference_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value=""><?php echo e(__('All Services')); ?></option>
                            <?php $__currentLoopData = $costReferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cr->id); ?>" <?php echo e($costReferenceId == $cr->id ? 'selected' : ''); ?>><?php echo e($cr->service_code); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label for="tariff_class_id" class="block text-sm font-medium text-gray-700"><?php echo e(__('Tariff Class')); ?></label>
                        <select id="tariff_class_id" name="tariff_class_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value=""><?php echo e(__('All Classes')); ?></option>
                            <?php $__currentLoopData = $tariffClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tc->id); ?>" <?php echo e($tariffClassId == $tc->id ? 'selected' : ''); ?>><?php echo e($tc->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <?php echo e(__('Filter')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <?php if($serviceVolumes->count() > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Period')); ?></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Service Code')); ?></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Service Description')); ?></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Tariff Class')); ?></th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Total Quantity')); ?></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__currentLoopData = $serviceVolumes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $volume): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($volume->period_month); ?>/<?php echo e($volume->period_year); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($volume->costReference ? $volume->costReference->service_code : '-'); ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo e($volume->costReference ? $volume->costReference->service_description : '-'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($volume->tariffClass ? $volume->tariffClass->name : '-'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right"><?php echo e(number_format($volume->total_quantity, 2, ',', '.')); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center gap-2">
                                                <a href="<?php echo e(route('service-volumes.show', $volume)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500" title="<?php echo e(__('View')); ?>" aria-label="<?php echo e(__('View')); ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                                                    </svg>
                                                </a>
                                                <a href="<?php echo e(route('service-volumes.edit', $volume)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500" title="<?php echo e(__('Edit')); ?>" aria-label="<?php echo e(__('Edit')); ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                                                    </svg>
                                                </a>
                                                <form action="<?php echo e(route('service-volumes.destroy', $volume)); ?>" method="POST" class="inline" onsubmit="return confirm('<?php echo e(__('Are you sure you want to delete this service volume?')); ?>')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500" title="<?php echo e(__('Delete')); ?>" aria-label="<?php echo e(__('Delete')); ?>">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                            <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1Zm-3 6h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 9Zm3 2v8h2v-8H9Zm4 0v8h2v-8h-2Z"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        <?php echo e($serviceVolumes->links()); ?>

                    </div>
                <?php else: ?>
                    <p class="text-gray-600"><?php echo e(__('No service volumes found.')); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/service-volumes/index.blade.php ENDPATH**/ ?>