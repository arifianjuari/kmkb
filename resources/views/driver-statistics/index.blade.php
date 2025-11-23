@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Driver Statistics') }}</h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('driver-statistics.import') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    {{ __('Import Excel') }}
                </a>
                <a href="{{ route('driver-statistics.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    {{ __('Export Excel') }}
                </a>
                <a href="{{ route('driver-statistics.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    {{ __('Add New Driver Statistic') }}
                </a>
            </div>
        </div>
        
        @if(session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('driver-statistics.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5">
                    <div>
                        <label for="period_year" class="block text-sm font-medium text-gray-700">{{ __('Year') }}</label>
                        <select id="period_year" name="period_year" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $periodYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="period_month" class="block text-sm font-medium text-gray-700">{{ __('Month') }}</label>
                        <select id="period_month" name="period_month" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">{{ __('All Months') }}</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $periodMonth == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="cost_center_id" class="block text-sm font-medium text-gray-700">{{ __('Cost Center') }}</label>
                        <select id="cost_center_id" name="cost_center_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">{{ __('All Cost Centers') }}</option>
                            @foreach($costCenters as $cc)
                                <option value="{{ $cc->id }}" {{ $costCenterId == $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="allocation_driver_id" class="block text-sm font-medium text-gray-700">{{ __('Allocation Driver') }}</label>
                        <select id="allocation_driver_id" name="allocation_driver_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">{{ __('All Drivers') }}</option>
                            @foreach($allocationDrivers as $ad)
                                <option value="{{ $ad->id }}" {{ $allocationDriverId == $ad->id ? 'selected' : '' }}>{{ $ad->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            {{ __('Filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($driverStatistics->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Period') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Cost Center') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Allocation Driver') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Value') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($driverStatistics as $stat)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stat->period_month }}/{{ $stat->period_year }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $stat->costCenter ? $stat->costCenter->name : '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $stat->allocationDriver ? $stat->allocationDriver->name . ' (' . $stat->allocationDriver->unit_measurement . ')' : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($stat->value, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('driver-statistics.show', $stat) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                                {{ __('View') }}
                                            </a>
                                            <a href="{{ route('driver-statistics.edit', $stat) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700 ml-2">
                                                {{ __('Edit') }}
                                            </a>
                                            <form action="{{ route('driver-statistics.destroy', $stat) }}" method="POST" class="inline ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700" onclick="return confirm('{{ __('Are you sure you want to delete this driver statistic?') }}')">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $driverStatistics->links() }}
                    </div>
                @else
                    <p class="text-gray-600">{{ __('No driver statistics found.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

