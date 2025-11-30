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
    <td class="px-6 py-4 text-sm font-medium text-gray-900">
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
                {{ $division->name }}
            </span>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        {{ $division->code ?? '-' }}
    </td>
    <td class="px-6 py-4 text-sm text-gray-500">
        {{ Str::limit($division->description, 50) ?? '-' }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $division->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ $division->is_active ? 'Active' : 'Inactive' }}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <a href="{{ route('divisions.edit', $division) }}" class="text-biru-dongker-800 hover:text-biru-dongker-900 mr-3">Edit</a>
        <form action="{{ route('divisions.destroy', $division) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this division?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
        </form>
    </td>
</tr>

@if($hasChildren)
    @foreach($children as $child)
        @include('divisions.partials.tree-row', ['division' => $child, 'allDivisions' => $allDivisions, 'level' => $level + 1])
    @endforeach
@endif

