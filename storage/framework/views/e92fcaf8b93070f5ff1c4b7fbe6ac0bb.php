<?php $__env->startSection('title', 'Pilih Rumah Sakit'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Pilih Rumah Sakit</h1>
        
        <p class="text-gray-600 mb-6">Sebagai Super Admin, Anda perlu memilih rumah sakit untuk mengakses data SIMRS.</p>
        
        <form action="<?php echo e(route('hospitals.select.set')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="mb-6">
                <label for="hospital_id" class="block text-sm font-medium text-gray-700 mb-2">Rumah Sakit</label>
                <select name="hospital_id" id="hospital_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    <option value="">-- Pilih Rumah Sakit --</option>
                    <?php $__currentLoopData = $hospitals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hospital): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($hospital->id); ?>"><?php echo e($hospital->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['hospital_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            
            <div class="flex items-center justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Pilih Rumah Sakit
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/hospitals/select.blade.php ENDPATH**/ ?>