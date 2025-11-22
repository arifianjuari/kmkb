<?php $__env->startSection('content'); ?>
<section class="mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e(__('Create Patient Case')); ?></h2>
            <a href="<?php echo e(route('cases.index')); ?>" class="btn-secondary">
                <?php echo e(__('Back to List')); ?>

            </a>
        </div>
            
        <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
            <div class="px-4 py-5 sm:p-6">
                <form action="<?php echo e(route('cases.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="medical_record_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Medical Record Number')); ?></label>
                            <div class="mt-1">
                                <input type="text" id="medical_record_number" name="medical_record_number" value="<?php echo e(old('medical_record_number')); ?>" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['medical_record_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <?php $__errorArgs = ['medical_record_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Patient ID')); ?></label>
                            <div class="mt-1">
                                <input type="text" id="patient_id" name="patient_id" value="<?php echo e(old('patient_id')); ?>" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['patient_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <?php $__errorArgs = ['patient_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 mt-6">
                        <div class="sm:col-span-3">
                            <label for="clinical_pathway_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Clinical Pathway')); ?></label>
                            <div class="mt-1">
                                <select id="clinical_pathway_id" name="clinical_pathway_id" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['clinical_pathway_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value=""><?php echo e(__('Select Pathway')); ?></option>
                                    <?php $__currentLoopData = $pathways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pathway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($pathway->id); ?>" <?php echo e(old('clinical_pathway_id') == $pathway->id ? 'selected' : ''); ?>>
                                            <?php echo e($pathway->name); ?> (<?php echo e($pathway->diagnosis_code); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php $__errorArgs = ['clinical_pathway_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="admission_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Admission Date')); ?></label>
                            <div class="mt-1">
                                <input type="date" id="admission_date" name="admission_date" value="<?php echo e(old('admission_date')); ?>" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['admission_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <?php $__errorArgs = ['admission_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 mt-6">
                        <div class="sm:col-span-3">
                            <label for="discharge_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Discharge Date')); ?></label>
                            <div class="mt-1">
                                <input type="date" id="discharge_date" name="discharge_date" value="<?php echo e(old('discharge_date')); ?>" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['discharge_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <?php $__errorArgs = ['discharge_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="primary_diagnosis" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Primary Diagnosis')); ?></label>
                            <div class="mt-1">
                                <input type="text" id="primary_diagnosis" name="primary_diagnosis" value="<?php echo e(old('primary_diagnosis')); ?>" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['primary_diagnosis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <?php $__errorArgs = ['primary_diagnosis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="ina_cbg_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('INA CBG Code')); ?></label>
                            <div class="mt-1 relative">
                                <input type="text" id="ina_cbg_code" name="ina_cbg_code" value="<?php echo e(old('ina_cbg_code')); ?>" required autocomplete="off" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['ina_cbg_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <div id="cbg-code-suggestions" class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-md overflow-hidden dark:bg-gray-700 hidden">
                                    <ul class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm dark:bg-gray-700">
                                    </ul>
                                </div>
                            </div>
                            <?php $__errorArgs = ['ina_cbg_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 mt-6">
                        <div class="sm:col-span-3">
                            <label for="actual_total_cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Actual Total Cost')); ?></label>
                            <div class="mt-1">
                                <input type="number" id="actual_total_cost" name="actual_total_cost" step="0.01" min="0" value="<?php echo e(old('actual_total_cost')); ?>" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['actual_total_cost'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <?php $__errorArgs = ['actual_total_cost'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 mt-6">
                        <div class="sm:col-span-3">
                            <label for="ina_cbg_tariff" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('INA CBG Tariff')); ?></label>
                            <div class="mt-1">
                                <input type="number" id="ina_cbg_tariff" name="ina_cbg_tariff" step="0.01" min="0" value="<?php echo e(old('ina_cbg_tariff')); ?>" required class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['ina_cbg_tariff'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <?php $__errorArgs = ['ina_cbg_tariff'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="additional_diagnoses" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Additional Diagnoses')); ?></label>
                        <div class="mt-1">
                            <textarea id="additional_diagnoses" name="additional_diagnoses" rows="3" class="py-2 px-3 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['additional_diagnoses'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> dark:bg-gray-700 dark:border-gray-600 dark:text-white"><?php echo e(old('additional_diagnoses')); ?></textarea>
                        </div>
                        <?php $__errorArgs = ['additional_diagnoses'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-2">
                        <a href="<?php echo e(route('cases.index')); ?>" class="btn-secondary">
                            <?php echo e(__('Cancel')); ?>

                        </a>
                        <button type="submit" class="btn-primary">
                            <?php echo e(__('Create Case')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cbgCodeInput = document.getElementById('ina_cbg_code');
        const cbgSuggestions = document.getElementById('cbg-code-suggestions');
        const tariffInput = document.getElementById('ina_cbg_tariff');
        
        let timeout = null;

        // If code already has value (e.g., after validation error), auto-fetch tariff on load
        if (cbgCodeInput && cbgCodeInput.value) {
            fetch(`/jkn-cbg-codes/tariff?code=${encodeURIComponent(cbgCodeInput.value)}`)
                .then(response => response.json())
                .then(tariffData => {
                    if (tariffData && typeof tariffData.tariff !== 'undefined' && tariffData.tariff !== null) {
                        tariffInput.value = tariffData.tariff;
                    }
                })
                .catch(() => {/* noop */});
        }
        
        cbgCodeInput.addEventListener('input', function() {
            const query = this.value;
            
            // Clear previous timeout
            if (timeout) {
                clearTimeout(timeout);
            }
            
            // If query is empty, hide suggestions
            if (!query) {
                cbgSuggestions.classList.add('hidden');
                return;
            }
            
            // Set new timeout for debouncing
            timeout = setTimeout(function() {
                fetch(`/jkn-cbg-codes/search?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        // Clear previous suggestions
                        cbgSuggestions.querySelector('ul').innerHTML = '';
                        
                        // Add new suggestions
                        if (data.length > 0) {
                            data.forEach(code => {
                                const li = document.createElement('li');
                                li.className = 'cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 dark:hover:bg-gray-600';
                                li.innerHTML = `
                                    <div class="flex items-center">
                                        <span class="font-normal block truncate dark:text-white">${code.code} - ${code.name}</span>
                                    </div>
                                `;
                                li.addEventListener('click', function() {
                                    cbgCodeInput.value = code.code;
                                    cbgSuggestions.classList.add('hidden');
                                    
                                    // Auto-fill tariff
                                    fetch(`/jkn-cbg-codes/tariff?code=${encodeURIComponent(code.code)}`)
                                        .then(response => response.json())
                                        .then(tariffData => {
                                            if (tariffData.tariff) {
                                                tariffInput.value = tariffData.tariff;
                                            }
                                        })
                                        .catch(err => {
                                            console.error('Failed to fetch tariff', err);
                                        });
                                });
                                cbgSuggestions.querySelector('ul').appendChild(li);
                            });
                            cbgSuggestions.classList.remove('hidden');
                        } else {
                            cbgSuggestions.classList.add('hidden');
                        }
                    })
                    .catch(err => {
                        console.error('Failed to fetch CBG code suggestions', err);
                        cbgSuggestions.classList.add('hidden');
                    });
            }, 300); // 300ms debounce
        });

        // Show initial suggestions when focusing the input (top results)
        cbgCodeInput.addEventListener('focus', function() {
            // If there's an existing timeout, clear it to avoid double fetch
            if (timeout) clearTimeout(timeout);
            const query = this.value || '';
            fetch(`/jkn-cbg-codes/search?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    cbgSuggestions.querySelector('ul').innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(code => {
                            const li = document.createElement('li');
                            li.className = 'cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 dark:hover:bg-gray-600';
                            li.innerHTML = `
                                <div class="flex items-center">
                                    <span class="font-normal block truncate dark:text-white">${code.code} - ${code.name}</span>
                                </div>
                            `;
                            li.addEventListener('click', function() {
                                cbgCodeInput.value = code.code;
                                cbgSuggestions.classList.add('hidden');
                                fetch(`/jkn-cbg-codes/tariff?code=${encodeURIComponent(code.code)}`)
                                    .then(response => response.json())
                                    .then(tariffData => {
                                        if (tariffData.tariff) {
                                            tariffInput.value = tariffData.tariff;
                                        }
                                    })
                                    .catch(err => console.error('Failed to fetch tariff', err));
                            });
                            cbgSuggestions.querySelector('ul').appendChild(li);
                        });
                        cbgSuggestions.classList.remove('hidden');
                    } else {
                        cbgSuggestions.classList.add('hidden');
                    }
                })
                .catch(err => console.error('Failed to fetch CBG code suggestions', err));
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!cbgCodeInput.contains(e.target) && !cbgSuggestions.contains(e.target)) {
                cbgSuggestions.classList.add('hidden');
            }
        });

        // Hide suggestions on Escape key
        cbgCodeInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cbgSuggestions.classList.add('hidden');
                cbgCodeInput.blur();
            }
        });

        // Slight delay on blur to allow click selection
        cbgCodeInput.addEventListener('blur', function() {
            setTimeout(() => cbgSuggestions.classList.add('hidden'), 150);
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/cases/create.blade.php ENDPATH**/ ?>