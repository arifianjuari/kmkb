@php
    // Get level from parameter or default to 0
    $level = $level ?? 0;
    // Get children from allCostCenters collection
    $children = isset($allCostCenters) ? $allCostCenters->where('parent_id', $costCenter->id)->sortBy('name') : collect();
    $hasChildren = $children->count() > 0;
    $uniqueId = 'cc-' . $costCenter->id;
@endphp

<tr class="hover:bg-gray-50 cost-center-row division-{{ $divisionId ?? '' }}" 
    data-cost-center-id="{{ $costCenter->id }}"
    data-level="{{ $level }}"
    data-parent-id="{{ $costCenter->parent_id ?? '' }}">
    <td class="px-6 py-2 text-sm text-gray-900 font-medium">
        <div class="flex items-center">
            {{-- Indentation based on level --}}
            @if($level > 0)
                @for($i = 0; $i < $level; $i++)
                    <span class="inline-block w-6"></span>
                @endfor
            @endif
            {{-- Expand/collapse button for items with children --}}
            @if($hasChildren)
                <button type="button" 
                        class="tree-toggle inline-flex items-center mr-2 cursor-pointer hover:bg-gray-200 rounded p-1 transition-colors" 
                        onclick="toggleCostCenterTree('{{ $uniqueId }}', this)"
                        aria-label="Toggle children">
                    <svg class="w-4 h-4 text-gray-600 chevron-icon chevron-down" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    <svg class="w-4 h-4 text-gray-600 chevron-icon chevron-right hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            @else
                @if($level > 0)
                    <span class="inline-block w-6 mr-2"></span>
                @endif
            @endif
            <span class="{{ $level > 0 ? 'text-gray-700' : 'text-gray-900 font-semibold' }}">
                {{ $costCenter->name }}
            </span>
        </div>
    </td>
    <td class="px-6 py-2 text-sm text-gray-500">
        {{ $costCenter->building_name ?? '-' }}
    </td>
    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $costCenter->floor ?? '-' }}</td>
    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $costCenter->tariffClass ? $costCenter->tariffClass->name : '-' }}</td>
    <td class="px-6 py-2 whitespace-nowrap text-sm">
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $costCenter->type == 'support' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
            {{ $costCenter->type == 'support' ? __('Support') : __('Revenue') }}
        </span>
    </td>
    <td class="px-6 py-2 whitespace-nowrap text-sm">
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $costCenter->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ $costCenter->is_active ? __('Active') : __('Inactive') }}
        </span>
    </td>
    <td class="px-6 py-2 whitespace-nowrap text-sm">
        <div class="flex items-center gap-2">
            <a href="{{ route('cost-centers.show', $costCenter) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('View') }}" aria-label="{{ __('View') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                </svg>
            </a>
            @if(!auth()->user()?->isObserver())
            <a href="{{ route('cost-centers.edit', $costCenter) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-biru-dongker-800 hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                </svg>
            </a>
            <form action="{{ route('cost-centers.destroy', $costCenter) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this cost center?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1Zm-3 6h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 9Zm3 2v8h2v-8H9Zm4 0v8h2v-8h-2Z"/>
                    </svg>
                </button>
            </form>
            @endif
        </div>
    </td>
</tr>

{{-- Render children recursively --}}
@if($hasChildren)
    @foreach($children as $child)
        @include('cost-centers.partials.tree-row', [
            'costCenter' => $child, 
            'divisionId' => $divisionId, 
            'allCostCenters' => $allCostCenters,
            'level' => $level + 1
        ])
    @endforeach
@endif
