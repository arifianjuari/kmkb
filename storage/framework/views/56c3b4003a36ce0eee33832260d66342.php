<?php $__env->startSection('title', 'Add Case Detail'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4">Add Case Detail for <?php echo e($case->patient_name); ?></h2>
    
    <form action="<?php echo e(route('cases.details.store', $case)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" id="custom_step_toggle" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-600">Add custom (non-standard) pathway step</span>
            </label>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div id="pathway_step_field">
                <label for="pathway_step_id" class="block text-sm font-medium text-gray-700">Pathway Step</label>
                <select name="pathway_step_id" id="pathway_step_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Select a step</option>
                    <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($step->id); ?>" 
                                data-description="<?php echo e($step->description); ?>" 
                                data-quantity="<?php echo e($step->quantity ?? 1); ?>" 
                                data-estimated-cost="<?php echo e($step->estimated_cost ?? 0); ?>" 
                                data-cost-reference-id="<?php echo e($step->cost_reference_id ?? ''); ?>"
                                data-service-code="<?php echo e($step->costReference ? $step->costReference->service_code : ''); ?>"
                                <?php echo e(old('pathway_step_id') == $step->id ? 'selected' : ''); ?>>
                            Day <?php echo e($step->step_order); ?>. <?php echo e($step->description); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['pathway_step_id'];
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
            
            <div>
                <label for="service_item" class="block text-sm font-medium text-gray-700">Service Item</label>
                <input type="text" name="service_item" id="service_item" value="<?php echo e(old('service_item')); ?>" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                <?php $__errorArgs = ['service_item'];
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
            
            <div>
                <label for="service_code" class="block text-sm font-medium text-gray-700">Service Code</label>
                <input type="text" name="service_code" id="service_code" value="<?php echo e(old('service_code')); ?>" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <?php $__errorArgs = ['service_code'];
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
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="pending" <?php echo e(old('status') == 'pending' ? 'selected' : ''); ?>>Belum Dilakukan</option>
                    <option value="completed" <?php echo e(old('status') == 'completed' ? 'selected' : ''); ?> selected>Sudah Dilakukan</option>
                    <option value="skipped" <?php echo e(old('status') == 'skipped' ? 'selected' : ''); ?>>Dilewati</option>
                </select>
                <?php $__errorArgs = ['status'];
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
            
            <div>
                <label for="performed" class="block text-sm font-medium text-gray-700">Performed</label>
                <input type="checkbox" name="performed" id="performed" value="1" <?php echo e(old('performed') ? 'checked' : ''); ?> class="mt-1 rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <span class="ml-2 text-sm text-gray-600">Mark as performed</span>
                <?php $__errorArgs = ['performed'];
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
            
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" name="quantity" id="quantity" value="<?php echo e(old('quantity')); ?>" min="1"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" step="1"
                <?php $__errorArgs = ['quantity'];
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
            
            <div>
                <label for="actual_cost" class="block text-sm font-medium text-gray-700">Actual Cost</label>
                <input type="number" name="actual_cost" id="actual_cost" value="<?php echo e(old('actual_cost')); ?>" min="0"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" step="1000"
                <?php $__errorArgs = ['actual_cost'];
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
            
            <div>
                <label for="service_date" class="block text-sm font-medium text-gray-700">Service Date</label>
                <input type="date" name="service_date" id="service_date" value="<?php echo e(old('service_date') ?? date('Y-m-d')); ?>"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <?php $__errorArgs = ['service_date'];
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
        </div>
        
        <div class="mt-6 flex items-center justify-end space-x-3">
            <a href="<?php echo e(route('cases.show', $case)); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Add Case Detail
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pathwayStepSelect = document.getElementById('pathway_step_id');
        const serviceItemInput = document.getElementById('service_item');
        const serviceCodeInput = document.getElementById('service_code');
        const quantityInput = document.getElementById('quantity');
        const actualCostInput = document.getElementById('actual_cost');
        const customStepToggle = document.getElementById('custom_step_toggle');
        const pathwayStepField = document.getElementById('pathway_step_field');
        
        // Function to toggle between standard and custom steps
        function toggleCustomStep() {
            if (customStepToggle.checked) {
                // Hide pathway step selection
                pathwayStepField.style.display = 'none';
                // Clear pathway step selection
                pathwayStepSelect.value = '';
                // Make service item editable and required
                serviceItemInput.readOnly = false;
                serviceItemInput.required = true;
                // Clear other fields
                serviceCodeInput.value = '';
                quantityInput.value = '';
                actualCostInput.value = '';
            } else {
                // Show pathway step selection
                pathwayStepField.style.display = 'block';
                // Make service item readonly again
                serviceItemInput.readOnly = true;
                serviceItemInput.required = false;
                // Clear other fields
                serviceCodeInput.value = '';
                quantityInput.value = '';
                actualCostInput.value = '';
            }
        }
        
        // Add event listener to the toggle
        customStepToggle.addEventListener('change', toggleCustomStep);
        
        pathwayStepSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value === '') {
                serviceItemInput.value = '';
                serviceCodeInput.value = '';
                quantityInput.value = '';
                actualCostInput.value = '';
                serviceItemInput.readOnly = !customStepToggle.checked; // Only readonly if not custom
                serviceCodeInput.readOnly = !customStepToggle.checked; // Only readonly if not custom
            } else {
                const description = selectedOption.getAttribute('data-description');
                const quantity = selectedOption.getAttribute('data-quantity');
                const estimatedCost = selectedOption.getAttribute('data-estimated-cost');
                const serviceCode = selectedOption.getAttribute('data-service-code');
                
                serviceItemInput.value = description || '';
                serviceCodeInput.value = serviceCode || '';
                quantityInput.value = quantity || 1;
                actualCostInput.value = estimatedCost || 0;
                
                // Make fields editable after selecting a pathway step
                serviceItemInput.readOnly = false;
                serviceCodeInput.readOnly = false;
            }
        });
        
        // Initialize on page load
        toggleCustomStep();
        
        // Trigger change event on page load if there's an old value
        if (pathwayStepSelect.value !== '') {
            pathwayStepSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/cases/details/create.blade.php ENDPATH**/ ?>