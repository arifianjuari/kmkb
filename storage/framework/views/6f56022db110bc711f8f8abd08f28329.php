<?php $__env->startSection('content'); ?>
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white"><?php echo e(__('Daftar Rumah Sakit')); ?></h2>
            <a href="<?php echo e(route('hospitals.create')); ?>" class="btn-primary">
                <?php echo e(__('Tambah Rumah Sakit')); ?>

            </a>
        </div>
        
        <div class="p-6">
            <?php if(session('status')): ?>
                <div class="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-900">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400 dark:text-green-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200"><?php echo e(session('status')); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Nama')); ?></th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Kode')); ?></th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Alamat')); ?></th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Kontak')); ?></th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Status')); ?></th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Aksi')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        <?php $__empty_1 = true; $__currentLoopData = $hospitals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hospital): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    <div class="flex items-center">
                                        <?php $logoPath = $hospital->logo_path; ?>
                                        <?php if($logoPath && Storage::disk('public')->exists($logoPath)): ?>
                                            <img src="<?php echo e(Storage::disk('public')->url($logoPath)); ?>" alt="<?php echo e($hospital->name); ?>" class="h-8 w-8 rounded-full mr-3">
                                        <?php else: ?>
                                            <?php if (isset($component)) { $__componentOriginal9abb5b9f9947fec1aec288b20ca02d30 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9abb5b9f9947fec1aec288b20ca02d30 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.hospital-avatar','data' => ['name' => ''.e($hospital->name).'','color' => ''.e($hospital->theme_color).'','size' => '8','class' => 'mr-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('hospital-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($hospital->name).'','color' => ''.e($hospital->theme_color).'','size' => '8','class' => 'mr-3']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9abb5b9f9947fec1aec288b20ca02d30)): ?>
<?php $attributes = $__attributesOriginal9abb5b9f9947fec1aec288b20ca02d30; ?>
<?php unset($__attributesOriginal9abb5b9f9947fec1aec288b20ca02d30); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9abb5b9f9947fec1aec288b20ca02d30)): ?>
<?php $component = $__componentOriginal9abb5b9f9947fec1aec288b20ca02d30; ?>
<?php unset($__componentOriginal9abb5b9f9947fec1aec288b20ca02d30); ?>
<?php endif; ?>
                                        <?php endif; ?>
                                        <span><?php echo e($hospital->name); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo e($hospital->code); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?php echo e(Str::limit($hospital->address, 50)); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo e($hospital->contact); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($hospital->is_active): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                                            <?php echo e(__('Aktif')); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100">
                                            <?php echo e(__('Nonaktif')); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?php echo e(route('hospitals.show', $hospital)); ?>" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3"><?php echo e(__('Lihat')); ?></a>
                                    <a href="<?php echo e(route('hospitals.edit', $hospital)); ?>" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3"><?php echo e(__('Edit')); ?></a>
                                    <form action="<?php echo e(route('hospitals.destroy', $hospital)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('<?php echo e(__('Apakah Anda yakin ingin menghapus rumah sakit ini?')); ?>')">
                                            <?php echo e(__('Hapus')); ?>

                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    <?php echo e(__('Tidak ada data rumah sakit.')); ?>

                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/hospitals/index.blade.php ENDPATH**/ ?>