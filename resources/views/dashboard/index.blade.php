@extends('layouts.app')

@section('content')
<section class="mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 sm:px-0">
        <!-- Header / Intro -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Dashboard') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-300">{{ __('Overview of statistics and recent activity.') }}</p>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Pathways -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Pathways') }}</p>
                        <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalPathways }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7v10a2 2 0 002 2h10a2 2 0 002-2V7" />
                        </svg>
                    </div>
                </div>
            </div>
            <!-- Total Cases -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Cases') }}</p>
                        <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalCases }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M7 7v10m10-10v10" />
                        </svg>
                    </div>
                </div>
            </div>
            <!-- Avg Compliance -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Avg. Compliance') }}</p>
                        <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($averageCompliance, 2) }}%</p>
                    </div>
                    <div class="p-3 rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m-4-4h8" />
                        </svg>
                    </div>
                </div>
            </div>
            <!-- Total Variance -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Variance') }}</p>
                        <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">Rp{{ number_format($totalCostVariance, 2) }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-yellow-50 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero / CTA -->
        <div class="mt-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-6 text-white shadow">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-semibold">{{ __('Welcome to KMKB Dashboard') }}</h3>
                    <p class="opacity-90 mt-1">{{ __('Get insights at a glance and drill into recent activity below.') }}</p>
                </div>
                <div class="hidden md:block opacity-80">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V9a2 2 0 012-2h2a2 2 0 012 2v10M7 20h10" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Charts Placeholders -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Compliance Trend (30 days)') }}</h4>
                <div class="h-56 mt-4 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded">
                    <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-300 text-sm mt-2">{{ __('Chart placeholder — integrate Chart.js') }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Cost Variance (30 days)') }}</h4>
                <div class="h-56 mt-4 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded">
                    <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-300 text-sm mt-2">{{ __('Chart placeholder — integrate Chart.js') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent & By Pathway -->
        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Recent Cases -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recent Cases') }}</h3>
                </div>
                <div class="p-6">
                    @if($recentCases->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('MRN') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Pathway') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Admission Date') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('Compliance') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($recentCases as $case)
                                        @php
                                            $comp = (float) $case->compliance_percentage;
                                            $badge = $comp >= 80 ? 'bg-green-100 text-green-800' : ($comp >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $case->medical_record_number }}</td>
                                            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $case->clinicalPathway->name }}</td>
                                            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $case->admission_date->format('d M Y') }}</td>
                                            <td class="px-6 py-3 whitespace-nowrap">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ number_format($comp, 2) }}%</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400">{{ __('No recent cases found.') }}</p>
                    @endif
                </div>
            </div>

            <!-- Cases by Pathway -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Cases by Pathway') }}</h3>
                </div>
                <div class="p-6">
                    @if($casesByPathway->count() > 0)
                        @php $maxCases = max(1, $casesByPathway->max('case_count')); @endphp
                        <ul class="space-y-4">
                            @foreach($casesByPathway as $data)
                                <li>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $data->pathway_name }}</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-200">{{ $data->case_count }}</span>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full bg-indigo-500" style="width: {{ ($data->case_count / $maxCases) * 100 }}%"></div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-600 dark:text-gray-400">{{ __('No cases found.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
