@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div>
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 whitespace-nowrap">{{ __('Allocation Results') }}</h2>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('allocation-results.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    {{ __('Export Excel') }}
                </a>
                <a href="{{ route('allocation.run.form') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                    {{ __('Run Allocation') }}
                </a>
            </div>
        </div>
        

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('allocation-results.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5">
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700">{{ __('Year') }}</label>
                        <select id="year" name="year" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700">{{ __('Month') }}</label>
                        <select id="month" name="month" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ str_pad($m, 2, '0', STR_PAD_LEFT) }} - {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    
                    <div>
                        <label for="source_cost_center_id" class="block text-sm font-medium text-gray-700">{{ __('Source Cost Center') }}</label>
                        <select id="source_cost_center_id" name="source_cost_center_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                            <option value="">{{ __('All Sources') }}</option>
                            @foreach($costCenters as $cc)
                                <option value="{{ $cc->id }}" {{ $sourceCostCenterId == $cc->id ? 'selected' : '' }}>
                                    {{ $cc->code }} - {{ $cc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="target_cost_center_id" class="block text-sm font-medium text-gray-700">{{ __('Target Cost Center') }}</label>
                        <select id="target_cost_center_id" name="target_cost_center_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 text-sm">
                            <option value="">{{ __('All Targets') }}</option>
                            @foreach($costCenters as $cc)
                                <option value="{{ $cc->id }}" {{ $targetCostCenterId == $cc->id ? 'selected' : '' }}>
                                    {{ $cc->code }} - {{ $cc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                            {{ __('Filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <p class="text-sm text-gray-500">Period</p>
                        <p class="text-lg font-semibold text-gray-900">{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Allocated</p>
                        <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($summary['total_allocated'], 2, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Records</p>
                        <p class="text-lg font-semibold text-gray-900">{{ number_format($summary['total_records'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($allocationResults->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Step') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Source Cost Center') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Target Cost Center') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Allocated Amount') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($allocationResults as $result)
                                    <tr>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-biru-dongker-300 text-biru-dongker-900">
                                                {{ ucfirst(str_replace('_', ' ', $result->allocation_step)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">
                                            <div>
                                                <div class="font-medium">{{ $result->sourceCostCenter->name ?? '-' }}</div>
                                                <div class="text-gray-500 text-xs">{{ $result->sourceCostCenter->code ?? '-' }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">
                                            <div>
                                                <div class="font-medium">{{ $result->targetCostCenter->name ?? '-' }}</div>
                                                <div class="text-gray-500 text-xs">{{ $result->targetCostCenter->code ?? '-' }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                                            Rp {{ number_format($result->allocated_amount, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm">
                                            <a href="{{ route('allocation-results.show', $result) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('View') }}" aria-label="{{ __('View') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                    <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $allocationResults->links() }}
                    </div>
                @else
                    <p class="text-gray-600">{{ __('No allocation results found for the selected period.') }}</p>
                    <a href="{{ route('allocation.run.form') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900">
                        {{ __('Run Allocation') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

