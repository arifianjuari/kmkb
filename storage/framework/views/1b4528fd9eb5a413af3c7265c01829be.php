<?php $__env->startSection('content'); ?>
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white"><?php echo e(__('Edit Rumah Sakit')); ?></h2>
        </div>
        
        <div class="p-6">
            <?php if(session('status')): ?>
                <div class="mb-4 rounded-md bg-green-50 p-4 border border-green-200">
                    <p class="text-sm text-green-800"><?php echo e(session('status')); ?></p>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
                    <p class="text-sm font-medium text-red-800 mb-2"><?php echo e(__('Terjadi kesalahan saat menyimpan.')); ?></p>
                    <ul class="list-disc list-inside text-sm text-red-700">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('hospitals.update', $hospital)); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Nama Rumah Sakit')); ?> *</label>
                            <input type="text" name="name" id="name" value="<?php echo e(old('name', $hospital->name)); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-4">
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Kode Rumah Sakit')); ?> *</label>
                            <input type="text" name="code" id="code" value="<?php echo e(old('code', $hospital->code)); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-4">
                            <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Logo')); ?></label>
                            <?php
                                $logoPath = $hospital->logo_path;
                                $isAbsoluteUrl = $logoPath && (Str::startsWith($logoPath, ['http://', 'https://']));
                                $normalizedPath = $logoPath;
                                if ($logoPath && (Str::startsWith($logoPath, '/storage/') || Str::startsWith($logoPath, 'storage/'))) {
                                    $normalizedPath = ltrim(Str::after($logoPath, '/storage/'), '/');
                                }
                            ?>
                            <?php if($isAbsoluteUrl || ($normalizedPath && Storage::disk('public')->exists($normalizedPath))): ?>
                                <div class="mb-2">
                                    <img src="<?php echo e($isAbsoluteUrl ? $logoPath : Storage::disk('public')->url($normalizedPath)); ?>" alt="<?php echo e($hospital->name); ?>" class="h-16 w-16 rounded-full">
                                </div>
                            <?php elseif($logoPath): ?>
                                <div class="mb-2">
                                    <?php if (isset($component)) { $__componentOriginal9abb5b9f9947fec1aec288b20ca02d30 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9abb5b9f9947fec1aec288b20ca02d30 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.hospital-avatar','data' => ['name' => ''.e($hospital->name).'','color' => ''.e($hospital->theme_color).'','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('hospital-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($hospital->name).'','color' => ''.e($hospital->theme_color).'','size' => '16']); ?>
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
                                </div>
                            <?php endif; ?>
                            <input type="file" name="logo" id="logo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-100 dark:hover:file:bg-blue-800">
                            <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    
                    <div>
                        <div class="mb-4">
                            <label for="theme_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Warna Tema')); ?></label>
                            <input type="color" name="theme_color" id="theme_color" value="<?php echo e(old('theme_color', $hospital->theme_color ?? '#2563eb')); ?>" class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                            <?php $__errorArgs = ['theme_color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Alamat')); ?></label>
                            <textarea name="address" id="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"><?php echo e(old('address', $hospital->address)); ?></textarea>
                            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-4">
                            <label for="contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Kontak')); ?></label>
                            <input type="text" name="contact" id="contact" value="<?php echo e(old('contact', $hospital->contact)); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <?php $__errorArgs = ['contact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <div class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input id="is_active" name="is_active" type="checkbox" value="1" <?php echo e(old('is_active', $hospital->is_active) ? 'checked' : ''); ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-white">
                            <?php echo e(__('Aktif')); ?>

                        </label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="<?php echo e(route('hospitals.index')); ?>" class="btn-secondary">
                        <?php echo e(__('Batal')); ?>

                    </a>
                    <button type="submit" class="btn-primary">
                        <?php echo e(__('Simpan Perubahan')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/hospitals/edit.blade.php ENDPATH**/ ?>