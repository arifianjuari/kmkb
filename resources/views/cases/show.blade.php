@extends('layouts.app')

@section('content')
<section class="mx-auto py-1 sm:px-6 lg:px-8">
    <div class="px-4 py-1 sm:px-0">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Patient Case Details') }}</h2>
            <div class="flex space-x-2">
                <a href="{{ route('cases.index') }}" class="btn-secondary">
                    {{ __('Back to List') }}
                </a>
                <a href="{{ route('cases.edit', $case) }}" class="btn-primary">
                    {{ __('Edit') }}
                </a>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6 dark:bg-gray-800">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">{{ __('Case Information') }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Medical Record Number') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">{{ $case->medical_record_number }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Patient ID') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">{{ $case->patient_id }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Clinical Pathway') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">{{ $case->clinicalPathway->name }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Primary Diagnosis') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">{{ $case->primary_diagnosis }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Admission Date') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">{{ $case->admission_date->format('d M Y') }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Discharge Date') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                    @if($case->discharge_date)
                                        {{ $case->discharge_date->format('d M Y') }}
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('Not Discharged') }}</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
                
                @if($case->additional_diagnoses)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300"><strong>{{ __('Additional Diagnoses') }}</strong></label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-300">{{ $case->additional_diagnoses }}</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6 dark:bg-gray-800">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">{{ __('Pathway Steps') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Standard steps defined in the clinical pathway') }}</p>
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
                @if($case->clinicalPathway && $case->clinicalPathway->steps->count() > 0)
                    @php
                        $categoryOrder = [
                            'Administrasi',
                            'Penilaian dan Pemantauan Medis',
                            'Penilaian dan Pemantauan Keperawatan',
                            'Pemeriksaan Penunjang Medik',
                            'Tindakan Medis',
                            'Tindakan Keperawatan',
                            'Medikasi',
                            'BHP',
                            'Nutrisi',
                            'Kegiatan',
                            'Konsultasi dan Komunikasi Tim',
                            'Konseling Psikososial',
                            'Pendidikan dan Komunikasi dengan Pasien/Keluarga',
                            'Kriteria KRS',
                        ];

                        $stepsByCategory = $case->clinicalPathway->steps->groupBy('category');
                        $stepsByDay = $case->clinicalPathway->steps->groupBy('step_order')->sortKeys();
                    @endphp

                    <div x-data="{ groupBy: 'category' }" class="space-y-4">
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-gray-600 dark:text-gray-300">{{ __('Group by') }}:</span>
                            <div class="flex items-center gap-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="groupByPathway" value="category" x-model="groupBy" class="mr-2 text-biru-dongker-800 focus:ring-biru-dongker-700">
                                    <span class="text-sm text-gray-700 dark:text-gray-200">{{ __('Category') }}</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="groupByPathway" value="day" x-model="groupBy" class="mr-2 text-biru-dongker-800 focus:ring-biru-dongker-700">
                                    <span class="text-sm text-gray-700 dark:text-gray-200">{{ __('Day') }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Group by Category -->
                        <div x-show="groupBy === 'category'" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Day') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Description') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Quantity') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Standard Cost') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Total Cost') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    @foreach($categoryOrder as $category)
                                        @if($stepsByCategory->has($category) && $stepsByCategory[$category]->count() > 0)
                                            <tr class="bg-gray-50 dark:bg-gray-700/40">
                                                <td colspan="5" class="px-6 py-3">
                                                    <h6 class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $category }}</h6>
                                                </td>
                                            </tr>
                                            @foreach($stepsByCategory[$category]->sortBy('step_order') as $step)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $step->step_order }}</td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">{{ $step->description }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $step->quantity ?? 1 }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">Rp{{ number_format($step->estimated_cost ?? 0, 0, ',', '.') }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">Rp{{ number_format(($step->estimated_cost ?? 0) * ($step->quantity ?? 1), 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach

                                    @if($stepsByCategory->has(null) && $stepsByCategory[null]->count() > 0)
                                        <tr class="bg-gray-50 dark:bg-gray-700/40">
                                            <td colspan="5" class="px-6 py-3">
                                                <h6 class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('Other') }}</h6>
                                            </td>
                                        </tr>
                                        @foreach($stepsByCategory[null]->sortBy('step_order') as $step)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $step->step_order }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">{{ $step->description }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $step->quantity ?? 1 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">Rp{{ number_format($step->estimated_cost ?? 0, 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">Rp{{ number_format(($step->estimated_cost ?? 0) * ($step->quantity ?? 1), 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Group by Day -->
                        <div x-show="groupBy === 'day'" class="overflow-x-auto" style="display: none;">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Day') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Description') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Quantity') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Standard Cost') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Total Cost') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    @foreach($stepsByDay as $day => $steps)
                                        <tr class="bg-gray-50 dark:bg-gray-700/40">
                                            <td colspan="5" class="px-6 py-3">
                                                <h6 class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('Day') }} {{ $day }}</h6>
                                            </td>
                                        </tr>
                                        @foreach($steps->sortBy('display_order') as $step)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $step->step_order }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">{{ $step->description }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $step->quantity ?? 1 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">Rp{{ number_format($step->estimated_cost ?? 0, 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">Rp{{ number_format(($step->estimated_cost ?? 0) * ($step->quantity ?? 1), 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">{{ __('No pathway steps defined.') }}</p>
                @endif
            </div>
        </div>
        
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mb-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">{{ __('Financial Information') }}</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    @php
                        // Precompute compliance and labels for this section
                        $pathwayStepsCount = $case->clinicalPathway->steps->count();
                        $usedSteps = $usedPathwayStepsCount ?? 0;
                        
                        // Count custom steps (non-compliance penalty)
                        $customStepsCount = $case->caseDetails->filter(function($detail) {
                            return $detail->isCustomStep();
                        })->count();

                        // Calculate compliance: (Used Pathway Steps - Custom Steps) / Pathway Steps Count × 100
                        // Custom steps reduce compliance as they indicate deviation from standard pathway
                        $computedCompliance = $pathwayStepsCount > 0
                            ? round((($usedSteps - $customStepsCount) / $pathwayStepsCount) * 100, 2)
                            : 100.00;
                        
                        // Ensure compliance doesn't go below 0%
                        $computedCompliance = max(0, $computedCompliance);

                        // Right-bottom and right-box label type
                        $bottomLabelType = $usedSteps < $pathwayStepsCount ? 'under' : ($usedSteps > $pathwayStepsCount ? 'over' : 'equal');
                    @endphp
                    @php
                        if (!isset($actualTotalCost)) {
                            // Only count case details that are performed (performed = 1 or true)
                            $actualTotalCost = $case->caseDetails->where('performed', 1)->sum(function($detail) {
                                return ($detail->actual_cost ?? 0) * ($detail->quantity ?? 1);
                            });
                        }
                        $fullStandardCost = $case->clinicalPathway->steps->sum(function($step) {
                            return ($step->estimated_cost ?? 0) * $step->quantity;
                        });
                        $computedVariance = ($case->ina_cbg_tariff ?? 0) - ($actualTotalCost ?? 0);
                    @endphp
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:order-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Full Standard Cost') }}</dt>
                            <dd class="mt-1 text-sm text-blue-600 dark:text-blue-400 font-semibold">
                                Rp{{ number_format($fullStandardCost, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="sm:order-2 sm:text-right">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('INA CBG Tariff') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                Rp{{ number_format($case->ina_cbg_tariff, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="sm:order-3">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Actual Total Cost') }}</dt>
                            <dd class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">
                                Rp{{ number_format($actualTotalCost, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="sm:order-4 sm:text-right">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Cost Variance') }}</dt>
                            <dd class="mt-1 text-xl font-semibold">
                                <span class="{{ $computedVariance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    Rp{{ number_format($computedVariance, 0, ',', '.') }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                    
                    <!-- Annotation Section -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <strong>{{ __('Annotation') }}</strong>
                        </label>
                        <div 
                            id="annotation-editor" 
                            data-case-id="{{ $case->id }}"
                            contenteditable="true"
                            class="min-h-[100px] p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 dark:bg-gray-700 dark:border-gray-600 text-sm text-gray-900 dark:text-gray-300"
                            style="white-space: pre-wrap; word-wrap: break-word;"
                        >{{ $case->annotation ?? '' }}</div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Click to edit annotation. Changes are saved automatically.') }}</p>
                        <div id="annotation-saving" class="mt-2 text-xs text-gray-500 dark:text-gray-400 hidden">
                            <span class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-3 w-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('Saving...') }}
                            </span>
                        </div>
                        <div id="annotation-saved" class="mt-2 text-xs text-green-600 dark:text-green-400 hidden">
                            {{ __('Saved') }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">{{ __('Pathway Information') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Compliance Percentage = ((Used Pathway Steps - Custom Steps) / Pathway Steps Count) × 100. Custom steps reduce compliance as they indicate deviation from standard pathway. If Used Pathway Steps are lower than Pathway Steps Count, it is categorized as Under-treatment. If higher, it is categorized as Over-treatment.') }}
                    </p>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                @php
                                    $customStepsCount = $case->caseDetails->filter(function($detail) {
                                        return $detail->isCustomStep();
                                    })->count();
                                    $standardStepsCount = $case->caseDetails->count() - $customStepsCount;
                                @endphp
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Case Steps Count') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                        {{ $case->caseDetails->count() }}
                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Pathway Steps Count') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                        {{ $case->clinicalPathway->steps->count() }}
                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Custom Steps Count') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                        {{ $customStepsCount }}
                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Standard Steps Count') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                        {{ $standardStepsCount }}
                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Used Pathway Steps') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                        {{ $usedPathwayStepsCount ?? 0 }}
                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Unused Pathway Steps') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                        {{ $unusedPathwayStepsCount ?? 0 }}
                                    </dd>
                                </div>
                                <!-- Compliance Percentage moved to right column -->
                            </dl>
                        </div>
                        <div class="flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('Compliance Percentage') }}</div>
                                <div class="font-extrabold {{ $computedCompliance >= 90 ? 'text-green-600 dark:text-green-400' : ($computedCompliance >= 70 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}"
                                     style="font-size: 42px; line-height: 1;">
                                    {{ number_format($computedCompliance, 2) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        @if($bottomLabelType === 'under')
                            <span class="whitespace-nowrap text-xl font-semibold text-yellow-700 dark:text-yellow-300">{{ __('Under-treatment') }}</span>
                        @elseif($bottomLabelType === 'over')
                            <span class="whitespace-nowrap text-xl font-semibold text-red-700 dark:text-red-300">{{ __('Over-treatment') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">{{ __('Case Details') }}</h3>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('cases.details.create', $case) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('Add Case Detail') }}
                    </a>
                    <form action="{{ route('cases.details.copy-steps', $case) }}" method="POST" onsubmit="return confirm('{{ __('Copy all pathway steps into this case? Existing mapped steps will be skipped.') }}')">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            {{ __('Copy Steps') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @php
                    $customStepsCount = $case->caseDetails->filter(function($detail) {
                        return $detail->isCustomStep();
                    })->count();
                @endphp
                @if($case->caseDetails->count() > 0)
                    @php
                        $caseCategoryOrder = [
                            'Administrasi',
                            'Penilaian dan Pemantauan Medis',
                            'Penilaian dan Pemantauan Keperawatan',
                            'Pemeriksaan Penunjang Medik',
                            'Tindakan Medis',
                            'Tindakan Keperawatan',
                            'Medikasi',
                            'BHP',
                            'Nutrisi',
                            'Kegiatan',
                            'Konsultasi dan Komunikasi Tim',
                            'Konseling Psikososial',
                            'Pendidikan dan Komunikasi dengan Pasien/Keluarga',
                            'Kriteria KRS',
                        ];

                        $caseDetailsByCategory = $case->caseDetails->groupBy(function($detail) {
                            if ($detail->isCustomStep()) {
                                return __('Custom Steps');
                            }
                            return $detail->pathwayStep->category ?? __('Other');
                        });

                        $caseDetailsByDay = $case->caseDetails->groupBy(function($detail) {
                            return $detail->isCustomStep() ? __('Custom') : ($detail->pathwayStep->step_order ?? __('Other'));
                        })->sortKeys();
                    @endphp

                    <div x-data="{ caseGroupBy: 'category' }" class="space-y-4">
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-gray-600 dark:text-gray-300">{{ __('Group by') }}:</span>
                            <div class="flex items-center gap-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="caseGroupBy" value="category" x-model="caseGroupBy" class="mr-2 text-biru-dongker-800 focus:ring-biru-dongker-700">
                                    <span class="text-sm text-gray-700 dark:text-gray-200">{{ __('Category') }}</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="caseGroupBy" value="day" x-model="caseGroupBy" class="mr-2 text-biru-dongker-800 focus:ring-biru-dongker-700">
                                    <span class="text-sm text-gray-700 dark:text-gray-200">{{ __('Day') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Actions') }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Day') }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Description') }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Case Quantity') }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Pathway Quantity') }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Variance') }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Performed') }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Actual Cost') }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Actual Cost Total') }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Standard Cost') }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Standard Cost Total') }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Status') }}</th>
                                    </tr>
                                </thead>

                                <!-- Group by Category -->
                                <tbody x-show="caseGroupBy === 'category'" class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    @foreach($caseCategoryOrder as $category)
                                        @if($caseDetailsByCategory->has($category) && $caseDetailsByCategory[$category]->count() > 0)
                                            <tr class="bg-gray-50 dark:bg-gray-700/40">
                                                <td colspan="12" class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                                    {{ $category }}
                                                </td>
                                            </tr>
                                            @foreach($caseDetailsByCategory[$category] as $detail)
                                                @include('cases.partials.detail_row', ['detail' => $detail, 'case' => $case])
                                            @endforeach
                                        @endif
                                    @endforeach

                                    @foreach($caseDetailsByCategory as $category => $details)
                                        @if(!in_array($category, $caseCategoryOrder, true))
                                            <tr class="bg-gray-50 dark:bg-gray-700/40">
                                                <td colspan="12" class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                                    {{ $category }}
                                                </td>
                                            </tr>
                                            @foreach($details as $detail)
                                                @include('cases.partials.detail_row', ['detail' => $detail, 'case' => $case])
                                            @endforeach
                                        @endif
                                    @endforeach
                                </tbody>

                                <!-- Group by Day -->
                                <tbody x-show="caseGroupBy === 'day'" class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" style="display: none;">
                                    @foreach($caseDetailsByDay as $day => $details)
                                        <tr class="bg-gray-50 dark:bg-gray-700/40">
                                            <td colspan="12" class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                                {{ is_numeric($day) ? __('Day') . ' ' . $day : $day }}
                                            </td>
                                        </tr>
                                        @foreach($details as $detail)
                                            @include('cases.partials.detail_row', ['detail' => $detail, 'case' => $case])
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">{{ __('No case details recorded yet.') }}</p>
                @endif
                
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
            
            // For performed field, save the checkbox state before clearing
            let originalCheckboxState = false;
            if (field === 'performed') {
                const checkbox = cell.querySelector('.inline-edit-checkbox');
                if (checkbox) {
                    originalCheckboxState = checkbox.checked;
                }
            }
            
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
                    // Update the displayed value appropriately
                    if (field === 'performed') {
                        // Keep checkbox for performed field so it can be edited again
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.className = 'inline-edit-checkbox';
                        checkbox.checked = value === '1';
                        checkbox.setAttribute('data-original-value', value);
                        
                        // Clear cell and add checkbox
                        cell.innerHTML = '';
                        cell.appendChild(checkbox);
                        
                        // Re-attach event listener for the new checkbox
                        checkbox.addEventListener('change', function() {
                            const id = this.closest('td').dataset.id;
                            const field = this.closest('td').dataset.field;
                            const value = this.checked ? '1' : '0';
                            const originalValue = this.getAttribute('data-original-value');
                            
                            if (value !== originalValue) {
                                updateCaseDetail(id, field, value);
                                this.setAttribute('data-original-value', value);
                            }
                        });
                    } else {
                        // For other fields, restore original content first
                        cell.innerHTML = originalContent;
                        
                        // Then update the displayed value appropriately
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
                    if (field === 'performed') {
                        // For performed field, recreate checkbox with event listener using saved state
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.className = 'inline-edit-checkbox';
                        checkbox.checked = originalCheckboxState;
                        checkbox.setAttribute('data-original-value', originalCheckboxState ? '1' : '0');
                        
                        cell.innerHTML = '';
                        cell.appendChild(checkbox);
                        
                        // Re-attach event listener
                        checkbox.addEventListener('change', function() {
                            const id = this.closest('td').dataset.id;
                            const field = this.closest('td').dataset.field;
                            const value = this.checked ? '1' : '0';
                            const originalValue = this.getAttribute('data-original-value');
                            
                            if (value !== originalValue) {
                                updateCaseDetail(id, field, value);
                                this.setAttribute('data-original-value', value);
                            }
                        });
                    } else {
                        cell.innerHTML = originalContent;
                    }
                    alert('Error updating case detail: ' + data.message);
                }
            })
            .catch(error => {
                // Show error message
                if (field === 'performed') {
                    // For performed field, recreate checkbox with event listener using saved state
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.className = 'inline-edit-checkbox';
                    checkbox.checked = originalCheckboxState;
                    checkbox.setAttribute('data-original-value', originalCheckboxState ? '1' : '0');
                    
                    cell.innerHTML = '';
                    cell.appendChild(checkbox);
                    
                    // Re-attach event listener
                    checkbox.addEventListener('change', function() {
                        const id = this.closest('td').dataset.id;
                        const field = this.closest('td').dataset.field;
                        const value = this.checked ? '1' : '0';
                        const originalValue = this.getAttribute('data-original-value');
                        
                        if (value !== originalValue) {
                            updateCaseDetail(id, field, value);
                            this.setAttribute('data-original-value', value);
                        }
                    });
                } else {
                    cell.innerHTML = originalContent;
                }
                alert('Error updating case detail: ' + error.message);
            });
        }
        
        function confirmDelete(detailId, message) {
            if (confirm(message)) {
                document.getElementById('delete-form-' + detailId).submit();
            }
        }
        
        // Annotation inline editing
        const annotationEditor = document.getElementById('annotation-editor');
        const annotationSaving = document.getElementById('annotation-saving');
        const annotationSaved = document.getElementById('annotation-saved');
        let annotationTimeout = null;
        let annotationOriginalValue = annotationEditor ? annotationEditor.textContent.trim() : '';
        
        if (annotationEditor) {
            annotationEditor.addEventListener('input', function() {
                // Clear previous timeout
                if (annotationTimeout) {
                    clearTimeout(annotationTimeout);
                }
                
                // Hide saved message
                if (annotationSaved) {
                    annotationSaved.classList.add('hidden');
                }
                
                // Show saving indicator after a short delay
                annotationTimeout = setTimeout(function() {
                    if (annotationSaving) {
                        annotationSaving.classList.remove('hidden');
                    }
                }, 500);
                
                // Save after user stops typing (debounce)
                clearTimeout(annotationTimeout);
                annotationTimeout = setTimeout(function() {
                    updateAnnotation();
                }, 1000); // 1 second debounce
            });
            
            annotationEditor.addEventListener('blur', function() {
                // Save immediately on blur
                if (annotationTimeout) {
                    clearTimeout(annotationTimeout);
                }
                updateAnnotation();
            });
        }
        
        function updateAnnotation() {
            if (!annotationEditor) return;
            
            const caseId = annotationEditor.getAttribute('data-case-id');
            const newValue = annotationEditor.textContent.trim();
            
            // Don't save if value hasn't changed
            if (newValue === annotationOriginalValue) {
                if (annotationSaving) {
                    annotationSaving.classList.add('hidden');
                }
                return;
            }
            
            fetch(`/cases/${caseId}/annotation`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    annotation: newValue
                })
            })
            .then(response => response.json())
            .then(data => {
                if (annotationSaving) {
                    annotationSaving.classList.add('hidden');
                }
                if (annotationSaved) {
                    annotationSaved.classList.remove('hidden');
                }
                annotationOriginalValue = newValue;
                
                // Hide saved message after 2 seconds
                setTimeout(function() {
                    if (annotationSaved) {
                        annotationSaved.classList.add('hidden');
                    }
                }, 2000);
            })
            .catch(error => {
                console.error('Error updating annotation:', error);
                if (annotationSaving) {
                    annotationSaving.classList.add('hidden');
                }
                if (annotationEditor) {
                    annotationEditor.textContent = annotationOriginalValue;
                }
                alert('Failed to save annotation. Please try again.');
            });
        }
    </script>
@endsection
