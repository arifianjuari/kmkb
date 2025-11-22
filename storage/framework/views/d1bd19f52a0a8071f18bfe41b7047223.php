<?php $__env->startSection('content'); ?>
<section class="mx-auto py-1 sm:px-6 lg:px-8">
    <div class="px-4 py-1 sm:px-0">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e(__('Patient Case Details')); ?></h2>
            <div class="flex space-x-2">
                <a href="<?php echo e(route('cases.index')); ?>" class="btn-secondary">
                    <?php echo e(__('Back to List')); ?>

                </a>
                <a href="<?php echo e(route('cases.edit', $case)); ?>" class="btn-primary">
                    <?php echo e(__('Edit')); ?>

                </a>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6 dark:bg-gray-800">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white"><?php echo e(__('Case Information')); ?></h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('Medical Record Number')); ?></dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300"><?php echo e($case->medical_record_number); ?></dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('Patient ID')); ?></dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300"><?php echo e($case->patient_id); ?></dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('Clinical Pathway')); ?></dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300"><?php echo e($case->clinicalPathway->name); ?></dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('Primary Diagnosis')); ?></dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300"><?php echo e($case->primary_diagnosis); ?></dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('Admission Date')); ?></dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300"><?php echo e($case->admission_date->format('d M Y')); ?></dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('Discharge Date')); ?></dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                    <?php if($case->discharge_date): ?>
                                        <?php echo e($case->discharge_date->format('d M Y')); ?>

                                    <?php else: ?>
                                        <span class="text-gray-500 dark:text-gray-400"><?php echo e(__('Not Discharged')); ?></span>
                                    <?php endif; ?>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
                
                <?php if($case->additional_diagnoses): ?>
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300"><strong><?php echo e(__('Additional Diagnoses')); ?></strong></label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-300"><?php echo e($case->additional_diagnoses); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6 dark:bg-gray-800">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white"><?php echo e(__('Pathway Steps')); ?></h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e(__('Standard steps defined in the clinical pathway')); ?></p>
                </div>
                <button id="togglePathwaySteps" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg id="expandIcon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <svg id="collapseIcon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                    </svg>
                </button>
            </div>
            <div id="pathwayStepsContent" class="px-4 py-5 sm:p-6 hidden">
                <?php if($case->clinicalPathway && $case->clinicalPathway->steps->count() > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Day')); ?></th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Description')); ?></th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Quantity')); ?></th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Standard Cost')); ?></th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Total Cost')); ?></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <?php $__currentLoopData = $case->clinicalPathway->steps->sortBy('step_order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300"><?php echo e($step->step_order); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300"><?php echo e($step->description); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300"><?php echo e($step->quantity ?? 1); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">Rp<?php echo e(number_format($step->estimated_cost ?? 0, 0, ',', '.')); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">Rp<?php echo e(number_format(($step->estimated_cost ?? 0) * ($step->quantity ?? 1), 0, ',', '.')); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 dark:text-gray-400"><?php echo e(__('No pathway steps defined.')); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mb-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white"><?php echo e(__('Financial Information')); ?></h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <?php
                        // Precompute compliance and labels for this section
                        $pathwayStepsCount = $case->clinicalPathway->steps->count();
                        $usedSteps = $usedPathwayStepsCount ?? 0;
                        $standardStepsOnlyCount = $case->caseDetails->filter(function($detail) {
                            return !$detail->isCustomStep();
                        })->count();

                        // Base compliance
                        $computedCompliance = $pathwayStepsCount > 0
                            ? round(($usedSteps / $pathwayStepsCount) * 100, 2)
                            : 100.00;

                        // Over-treatment penalty (based on standard steps exceeding used steps)
                        $isOverTreatment = $standardStepsOnlyCount > $usedSteps;
                        $overTreatmentCount = $isOverTreatment ? ($standardStepsOnlyCount - $usedSteps) : 0;
                        if ($overTreatmentCount > 0 && $pathwayStepsCount > 0) {
                            $overTreatmentPenalty = round(($overTreatmentCount / $pathwayStepsCount) * 100, 2);
                            $computedCompliance = max(0, $computedCompliance - $overTreatmentPenalty);
                        }

                        // Right-bottom and right-box label type
                        $bottomLabelType = $usedSteps < $pathwayStepsCount ? 'under' : ($usedSteps > $pathwayStepsCount ? 'over' : 'equal');
                    ?>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('Actual Total Cost')); ?></dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                        <?php
                                            $actualTotalCost = $case->caseDetails->sum(function($detail) {
                                                return ($detail->actual_cost ?? 0) * ($detail->quantity ?? 1);
                                            });
                                        ?>
                                        Rp<?php echo e(number_format($actualTotalCost, 0, ',', '.')); ?>

                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('INA CBG Tariff')); ?></dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">Rp<?php echo e(number_format($case->ina_cbg_tariff, 0, ',', '.')); ?></dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('Full Standard Cost')); ?></dt>
                                    <dd class="mt-1 text-sm">
                                        <?php
                                            $fullStandardCost = $case->clinicalPathway->steps->sum(function($step) {
                                                return ($step->estimated_cost ?? 0) * $step->quantity;
                                            });
                                        ?>
                                        <span class="text-blue-600 dark:text-blue-400 font-semibold">
                                            Rp<?php echo e(number_format($fullStandardCost, 0, ',', '.')); ?>

                                        </span>
                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('Cost Variance')); ?></dt>
                                    <dd class="mt-1 text-sm">
                                        <?php
                                            // Use the already computed $actualTotalCost if available; otherwise compute here
                                            if (!isset($actualTotalCost)) {
                                                $actualTotalCost = $case->caseDetails->sum(function($detail) {
                                                    return ($detail->actual_cost ?? 0) * ($detail->quantity ?? 1);
                                                });
                                            }
                                            $computedVariance = ($case->ina_cbg_tariff ?? 0) - ($actualTotalCost ?? 0);
                                        ?>
                                        <span class="<?php echo e($computedVariance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'); ?> font-semibold">
                                            Rp<?php echo e(number_format($computedVariance, 0, ',', '.')); ?>

                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white"><?php echo e(__('Pathway Information')); ?></h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        <?php echo e(__('Compliance Percentage = (Used Pathway Steps / Pathway Steps Count) × 100. If Used Pathway Steps are lower than Pathway Steps Count, it is categorized as Under-treatment. If higher, it is categorized as Over-treatment.')); ?>

                    </p>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                <?php
                                    $customStepsCount = $case->caseDetails->filter(function($detail) {
                                        return $detail->isCustomStep();
                                    })->count();
                                    $standardStepsCount = $case->caseDetails->count() - $customStepsCount;
                                ?>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('Case Steps Count')); ?></dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                        <?php echo e($case->caseDetails->count()); ?>

                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('Pathway Steps Count')); ?></dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                        <?php echo e($case->clinicalPathway->steps->count()); ?>

                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('Custom Steps Count')); ?></dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                        <?php echo e($customStepsCount); ?>

                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('Standard Steps Count')); ?></dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                        <?php echo e($standardStepsCount); ?>

                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('Used Pathway Steps')); ?></dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                        <?php echo e($usedPathwayStepsCount ?? 0); ?>

                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('Unused Pathway Steps')); ?></dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                        <?php echo e($unusedPathwayStepsCount ?? 0); ?>

                                    </dd>
                                </div>
                                <!-- Compliance Percentage moved to right column -->
                            </dl>
                        </div>
                        <div class="flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1"><?php echo e(__('Compliance Percentage')); ?></div>
                                <div class="font-extrabold <?php echo e($computedCompliance >= 90 ? 'text-green-600 dark:text-green-400' : ($computedCompliance >= 70 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400')); ?>"
                                     style="font-size: 42px; line-height: 1;">
                                    <?php echo e(number_format($computedCompliance, 2)); ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <?php if($bottomLabelType === 'under'): ?>
                            <span class="whitespace-nowrap text-xl font-semibold text-yellow-700 dark:text-yellow-300"><?php echo e(__('Under-treatment')); ?></span>
                        <?php elseif($bottomLabelType === 'over'): ?>
                            <span class="whitespace-nowrap text-xl font-semibold text-red-700 dark:text-red-300"><?php echo e(__('Over-treatment')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white"><?php echo e(__('Case Details')); ?></h3>
                <div class="flex items-center space-x-2">
                    <a href="<?php echo e(route('cases.details.create', $case)); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <?php echo e(__('Add Case Detail')); ?>

                    </a>
                    <form action="<?php echo e(route('cases.details.copy-steps', $case)); ?>" method="POST" onsubmit="return confirm('<?php echo e(__('Copy all pathway steps into this case? Existing mapped steps will be skipped.')); ?>')">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <?php echo e(__('Copy Steps')); ?>

                        </button>
                    </form>
                </div>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <?php
                    $customStepsCount = $case->caseDetails->filter(function($detail) {
                        return $detail->isCustomStep();
                    })->count();
                ?>
                <?php if($case->caseDetails->count() > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Actions')); ?></th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Day')); ?></th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Description')); ?></th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Case Quantity')); ?></th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Pathway Quantity')); ?></th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Variance')); ?></th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Status')); ?></th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Performed')); ?></th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Actual Cost')); ?></th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Actual Cost Total')); ?></th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Standard Cost')); ?></th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Standard Cost Total')); ?></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <?php $__currentLoopData = $case->caseDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            <div class="flex space-x-2">
                                                <!-- Edit Icon -->
                                                <a href="<?php echo e(route('cases.details.edit', [$case, $detail])); ?>" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="<?php echo e(__('Edit')); ?>">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                
                                                <!-- Delete Icon -->
                                                <form id="delete-form-<?php echo e($detail->id); ?>" action="<?php echo e(route('cases.details.delete', [$case, $detail])); ?>" method="POST" class="inline">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="button" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="confirmDelete(<?php echo e($detail->id); ?>, '<?php echo e(__('Are you sure you want to delete this case detail?')); ?>')" title="<?php echo e(__('Delete')); ?>">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            <?php if($detail->isCustomStep()): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                    Custom
                                                </span>
                                            <?php else: ?>
                                                <?php echo e($detail->pathwayStep->step_order); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            <?php if($detail->isCustomStep()): ?>
                                                <strong><?php echo e($detail->service_item); ?></strong>
                                            <?php else: ?>
                                                <?php echo e($detail->pathwayStep->description); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300" data-field="quantity" data-id="<?php echo e($detail->id); ?>" contenteditable="true"><?php echo e($detail->quantity); ?></td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            <?php if($detail->isCustomStep()): ?>
                                                <span class="text-gray-500 dark:text-gray-400"><?php echo e(__('N/A')); ?></span>
                                            <?php else: ?>
                                                <?php echo e($detail->pathwayStep->quantity ?? 0); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            <?php if($detail->actual_cost !== null): ?>
                                                <?php
                                                    $standardCost = $detail->pathwayStep->costReference->standard_cost ?? 0;
                                                    $standardCostTotal = $standardCost * $detail->quantity;
                                                    $actualCostTotal = $detail->actual_cost * $detail->quantity;
                                                    $variance = $standardCostTotal - $actualCostTotal;
                                                ?>
                                                <span class="<?php echo e($variance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'); ?> font-semibold">
                                                    Rp<?php echo e(number_format($variance, 0, ',', '.')); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-gray-500 dark:text-gray-400"><?php echo e(__('N/A')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm" data-field="status" data-id="<?php echo e($detail->id); ?>">
                                            <select class="inline-edit-select" data-original-value="<?php echo e($detail->status); ?>">
                                                <option value="pending" <?php echo e($detail->status === 'pending' ? 'selected' : ''); ?>><?php echo e(__('Pending')); ?></option>
                                                <option value="completed" <?php echo e($detail->status === 'completed' ? 'selected' : ''); ?>><?php echo e(__('Completed')); ?></option>
                                                <option value="skipped" <?php echo e($detail->status === 'skipped' ? 'selected' : ''); ?>><?php echo e(__('Skipped')); ?></option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm" data-field="performed" data-id="<?php echo e($detail->id); ?>">
                                            <input type="checkbox" class="inline-edit-checkbox" <?php echo e($detail->performed ? 'checked' : ''); ?> data-original-value="<?php echo e($detail->performed ? '1' : '0'); ?>">
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300" data-field="actual_cost" data-id="<?php echo e($detail->id); ?>" contenteditable="true"><?php echo e($detail->actual_cost !== null ? 'Rp' . number_format($detail->actual_cost, 0, ',', '.') : ''); ?></td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">Rp<?php echo e(number_format($detail->actual_cost * $detail->quantity, 0, ',', '.')); ?></td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            Rp<?php echo e(number_format($detail->pathwayStep->costReference->standard_cost ?? 0, 0, ',', '.')); ?>

                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            <?php if($detail->isCustomStep()): ?>
                                                <span class="text-gray-500 dark:text-gray-400"><?php echo e(__('N/A')); ?></span>
                                            <?php else: ?>
                                                <?php
                                                    $pathwayStandardCost = $detail->pathwayStep->costReference->standard_cost ?? 0;
                                                    $pathwayQuantity = $detail->pathwayStep->quantity ?? 0;
                                                    $pathwayStandardCostTotal = $pathwayStandardCost * $pathwayQuantity;
                                                ?>
                                                Rp<?php echo e(number_format($pathwayStandardCostTotal, 0, ',', '.')); ?>

                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 dark:text-gray-400"><?php echo e(__('No case details recorded yet.')); ?></p>
                <?php endif; ?>
                
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('togglePathwaySteps');
            const content = document.getElementById('pathwayStepsContent');
            const expandIcon = document.getElementById('expandIcon');
            const collapseIcon = document.getElementById('collapseIcon');
            
            toggleButton.addEventListener('click', function() {
                // Toggle content visibility
                content.classList.toggle('hidden');
                
                // Toggle icons
                expandIcon.classList.toggle('hidden');
                collapseIcon.classList.toggle('hidden');
            });
            
            // Inline editing functionality
            setupInlineEditing();
        });
        
        function setupInlineEditing() {
            // Handle contenteditable cells
            const editableCells = document.querySelectorAll('td[contenteditable="true"]');
            editableCells.forEach(cell => {
                cell.addEventListener('focus', function() {
                    // Store original value
                    this.setAttribute('data-original-value', this.textContent);
                    
                    // For cost fields, remove formatting when editing
                    if (this.dataset.field === 'actual_cost') {
                        const value = this.textContent.replace(/[^0-9]/g, '');
                        this.textContent = value;
                    }
                });
                
                cell.addEventListener('blur', function() {
                    const id = this.dataset.id;
                    const field = this.dataset.field;
                    const value = this.textContent;
                    const originalValue = this.getAttribute('data-original-value');
                    
                    // For cost fields, reformat on blur
                    if (field === 'actual_cost') {
                        if (value !== '') {
                            const numericValue = parseFloat(value.replace(/[^0-9]/g, ''));
                            if (!isNaN(numericValue)) {
                                this.textContent = 'Rp' + numericValue.toLocaleString('id-ID');
                            }
                        }
                    }
                    
                    // Only send update if value changed
                    if (value !== originalValue) {
                        updateCaseDetail(id, field, value);
                    }
                });
                
                cell.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.blur();
                    }
                });
            });
            
            // Handle select dropdowns
            const selectElements = document.querySelectorAll('.inline-edit-select');
            selectElements.forEach(select => {
                select.addEventListener('change', function() {
                    const id = this.closest('td').dataset.id;
                    const field = this.closest('td').dataset.field;
                    const value = this.value;
                    const originalValue = this.getAttribute('data-original-value');
                    
                    // Only send update if value changed
                    if (value !== originalValue) {
                        updateCaseDetail(id, field, value);
                        this.setAttribute('data-original-value', value);
                    }
                });
            });
            
            // Handle checkboxes
            const checkboxElements = document.querySelectorAll('.inline-edit-checkbox');
            checkboxElements.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const id = this.closest('td').dataset.id;
                    const field = this.closest('td').dataset.field;
                    const value = this.checked ? '1' : '0';
                    const originalValue = this.getAttribute('data-original-value');
                    
                    // Only send update if value changed
                    if (value !== originalValue) {
                        updateCaseDetail(id, field, value);
                        this.setAttribute('data-original-value', value);
                    }
                });
            });
        }
        
        function updateCaseDetail(id, field, value) {
            // Show saving indicator
            const cell = document.querySelector(`td[data-id="${id}"][data-field="${field}"]`);
            const originalContent = cell.innerHTML;
            cell.innerHTML = '<span class="text-blue-500">Saving...</span>';
            
            // Send AJAX request
            fetch(`/cases/${window.location.pathname.split('/')[2]}/details/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    [field]: field === 'actual_cost' ? parseFloat(value.replace(/[^0-9]/g, '')) : value,
                    _method: 'PUT'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    cell.innerHTML = originalContent;
                    
                    // Update the displayed value appropriately
                    if (field === 'actual_cost') {
                        const numericValue = parseFloat(value.replace(/[^0-9]/g, ''));
                        if (!isNaN(numericValue)) {
                            cell.textContent = 'Rp' + numericValue.toLocaleString('id-ID');
                        }
                    } else if (field === 'quantity') {
                        cell.textContent = value;
                    } else if (field === 'status') {
                        // Update status display
                        const statusDisplays = {
                            'pending': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Pending</span>',
                            'completed': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Completed</span>',
                            'skipped': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Skipped</span>'
                        };
                        cell.innerHTML = statusDisplays[value];
                    } else if (field === 'performed') {
                        // Update performed display
                        if (value === '1') {
                            cell.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Yes</span>';
                        } else {
                            cell.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">No</span>';
                        }
                    }
                    
                    // Show brief success indicator
                    const originalBg = cell.style.backgroundColor;
                    cell.style.backgroundColor = '#d1fae5'; // green-100
                    setTimeout(() => {
                        cell.style.backgroundColor = originalBg;
                    }, 1000);
                } else {
                    // Show error message
                    cell.innerHTML = originalContent;
                    alert('Error updating case detail: ' + data.message);
                }
            })
            .catch(error => {
                // Show error message
                cell.innerHTML = originalContent;
                alert('Error updating case detail: ' + error.message);
            });
        }
        
        function confirmDelete(detailId, message) {
            if (confirm(message)) {
                document.getElementById('delete-form-' + detailId).submit();
            }
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/cases/show.blade.php ENDPATH**/ ?>