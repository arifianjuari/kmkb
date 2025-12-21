@php
    // Get children from filtered allDivisions
    $children = $allDivisions->where('parent_id', $division->id)->sortBy('name');
    $hasChildren = $children->count() > 0;
    $uniqueId = 'div-' . str_replace('-', '', $division->id);
@endphp

<tr class="hover:bg-gray-50 division-row" 
    data-division-id="{{ $division->id }}" 
    data-level="{{ $level }}"
    data-parent-id="{{ $division->parent_id ?? '' }}"
    data-children-container="{{ $uniqueId }}-children">
    <td class="px-6 py-2 text-sm font-medium text-gray-900">
        <div class="flex items-center">
            @if($level > 0)
                @for($i = 0; $i < $level; $i++)
                    <span class="inline-block w-6"></span>
                @endfor
            @endif
            @if($hasChildren)
                <button type="button" 
                        class="tree-toggle inline-flex items-center mr-2 cursor-pointer hover:bg-gray-200 rounded p-1 transition-colors" 
                        data-target="{{ $uniqueId }}-children"
                        onclick="toggleDivisionTree('{{ $uniqueId }}', this)"
                        aria-label="Toggle children">
                    <svg class="w-4 h-4 text-gray-600 chevron-icon chevron-down hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    <svg class="w-4 h-4 text-gray-600 chevron-icon chevron-right" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            @else
                @if($level > 0)
                    <span class="inline-block w-6 mr-2"></span>
                @endif
            @endif
            <span class="{{ $level > 0 ? 'text-gray-700' : 'text-gray-900 font-semibold' }}">
                {{ $division->name }}
            </span>
            @if($hasChildren)
                <span class="ml-1 text-xs text-gray-500">({{ $children->count() }})</span>
            @endif
        </div>
    </td>
    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
        {{ $division->code ?? '-' }}
    </td>
    <td class="px-6 py-2 text-sm text-gray-500">
        {{ Str::limit($division->description, 50) ?? '-' }}
    </td>
    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $division->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ $division->is_active ? 'Active' : 'Inactive' }}
        </span>
    </td>
    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium">
        <div class="flex items-center gap-2">
            <a href="{{ route('divisions.edit', $division) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-biru-dongker-800 hover:bg-biru-dongker-200 focus:outline-none focus:ring-2 focus:ring-biru-dongker-700" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                </svg>
            </a>
            <form action="{{ route('divisions.destroy', $division) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this organization unit?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1Zm-3 6h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 9Zm3 2v8h2v-8H9Zm4 0v8h2v-8h-2Z"/>
                    </svg>
                </button>
            </form>
        </div>
    </td>
</tr>

@if($hasChildren)
    @foreach($children as $child)
        @include('divisions.partials.tree-row', ['division' => $child, 'allDivisions' => $allDivisions, 'level' => $level + 1])
    @endforeach
@endif

