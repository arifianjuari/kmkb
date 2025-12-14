@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Organization Units') }}</h2>
        <a href="{{ route('divisions.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-biru-dongker-800 hover:bg-biru-dongker-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            {{ __('Add New Organization Unit') }}
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex-1 min-w-0">
                <form action="{{ route('divisions.index') }}" method="GET" class="flex gap-4">
                    <input type="hidden" name="view_mode" value="{{ $viewMode ?? 'tree' }}">
                    <div class="flex-1">
                        <label for="search" class="sr-only">Search</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ $search }}" class="focus:ring-biru-dongker-700 focus:border-biru-dongker-700 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="Search by name or code">
                        </div>
                    </div>
                    <div>
                        <select name="is_active" onchange="this.form.submit()" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-biru-dongker-700 focus:border-biru-dongker-700 sm:text-sm rounded-md">
                            <option value="">All Status</option>
                            <option value="1" {{ $isActive === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $isActive === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biru-dongker-700">
                        Filter
                    </button>
                </form>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('divisions.index', array_merge(request()->query(), ['view_mode' => 'diagram'])) }}" class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium {{ ($viewMode ?? 'tree') === 'diagram' ? 'bg-biru-dongker-800 text-white border-biru-dongker-800' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    Diagram
                </a>
                <a href="{{ route('divisions.index', array_merge(request()->query(), ['view_mode' => 'tree'])) }}" class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium {{ ($viewMode ?? 'tree') === 'tree' ? 'bg-biru-dongker-800 text-white border-biru-dongker-800' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    Tree
                </a>
                <a href="{{ route('divisions.index', array_merge(request()->query(), ['view_mode' => 'flat'])) }}" class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium {{ ($viewMode ?? 'tree') === 'flat' ? 'bg-biru-dongker-800 text-white border-biru-dongker-800' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    Flat
                </a>
            </div>
        </div>
        
        @if(isset($rootDivisions) && ($viewMode ?? 'tree') === 'diagram')
            {{-- Diagram View --}}
            <div class="p-6">
                @if($rootDivisions->count() > 0)
                    <div id="org-chart" class="org-chart-container">
                        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px;">
                            @foreach($rootDivisions as $rootDivision)
                                @include('divisions.partials.diagram-node', ['division' => $rootDivision, 'allDivisions' => $allDivisions, 'level' => 0])
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-gray-600 text-center py-8">{{ __('No organization units found.') }}</p>
                @endif
            </div>

            @push('styles')
            <style>
                .org-chart-container {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    min-height: 400px;
                    padding: 40px 20px;
                    overflow-x: auto;
                    overflow-y: visible;
                    background: #f9fafb;
                    border-radius: 8px;
                }

                .org-node {
                    position: relative;
                    margin: 0 10px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding-top: 20px;
                }

                .org-node-box {
                    padding: 14px 24px;
                    border-radius: 6px;
                    text-align: center;
                    min-width: 160px;
                    max-width: 220px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    cursor: pointer;
                    transition: all 0.3s ease;
                    border: 2px solid #9ca3af;
                    background: white;
                    color: #1f2937;
                    font-size: 12px;
                    font-weight: 600;
                    word-wrap: break-word;
                    position: relative;
                    z-index: 2;
                    margin-bottom: 0;
                }

                .org-node-box:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
                    border-color: #6b7280;
                }

                .org-node-box.inactive {
                    opacity: 0.5;
                    background: #f3f4f6;
                    color: #9ca3af;
                    border-color: #d1d5db;
                }

                .org-node-children {
                    display: flex;
                    justify-content: center;
                    align-items: flex-start;
                    margin-top: 20px;
                    position: relative;
                    padding-top: 10px;
                }

                /* Vertical connector from parent to children (above box) */
                .org-connector-vertical {
                    position: absolute;
                    top: 0;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 3px;
                    height: 20px;
                    background: #6b7280;
                    z-index: 1;
                    display: block;
                }

                /* Horizontal connector connecting siblings */
                .org-connector-horizontal {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 3px;
                    background: #6b7280;
                    z-index: 1;
                    display: block;
                }

                /* Vertical connector from horizontal line to parent (in children container) */
                .org-connector-vertical-up {
                    position: absolute;
                    top: 0;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 3px;
                    height: 20px;
                    background: #6b7280;
                    z-index: 1;
                    display: block;
                }
            </style>
            @endpush
        @elseif(isset($rootDivisions) && ($viewMode ?? 'tree') === 'tree')
            {{-- Tree View --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rootDivisions as $division)
                            @include('divisions.partials.tree-row', ['division' => $division, 'allDivisions' => $allDivisions, 'level' => 0])
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No organization units found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @push('scripts')
            <script>
                function toggleDivisionTree(containerId, button) {
                    // Find all rows that belong to this parent
                    const parentRow = button.closest('tr');
                    const parentId = parentRow.getAttribute('data-division-id');
                    
                    // Find all child rows (rows with data-parent-id matching this division's id)
                    const allRows = document.querySelectorAll('tr.division-row');
                    const childRows = Array.from(allRows).filter(row => {
                        return row.getAttribute('data-parent-id') === parentId;
                    });
                    
                    const chevronDown = button.querySelector('.chevron-down');
                    const chevronRight = button.querySelector('.chevron-right');
                    
                    // Check if currently expanded (first child is visible)
                    const isExpanded = childRows.length > 0 && !childRows[0].classList.contains('hidden');
                    
                    if (isExpanded) {
                        // Collapse: hide all children and their descendants
                        childRows.forEach(row => {
                            hideRowAndDescendants(row);
                        });
                        if (chevronDown) chevronDown.classList.add('hidden');
                        if (chevronRight) chevronRight.classList.remove('hidden');
                    } else {
                        // Expand: show direct children only
                        childRows.forEach(row => {
                            row.classList.remove('hidden');
                            // Reset chevron state for direct children (they should show as expanded)
                            const childButton = row.querySelector('.tree-toggle');
                            if (childButton) {
                                const childChevronDown = childButton.querySelector('.chevron-down');
                                const childChevronRight = childButton.querySelector('.chevron-right');
                                if (childChevronDown && childChevronRight) {
                                    // Check if child has visible children
                                    const childId = row.getAttribute('data-division-id');
                                    const grandChildren = Array.from(allRows).filter(r => {
                                        return r.getAttribute('data-parent-id') === childId;
                                    });
                                    const hasVisibleGrandChildren = grandChildren.length > 0 && !grandChildren[0].classList.contains('hidden');
                                    
                                    if (hasVisibleGrandChildren) {
                                        childChevronDown.classList.remove('hidden');
                                        childChevronRight.classList.add('hidden');
                                    } else {
                                        childChevronDown.classList.remove('hidden');
                                        childChevronRight.classList.add('hidden');
                                    }
                                }
                            }
                        });
                        if (chevronDown) chevronDown.classList.remove('hidden');
                        if (chevronRight) chevronRight.classList.add('hidden');
                    }
                }
                
                function hideRowAndDescendants(row) {
                    row.classList.add('hidden');
                    const divisionId = row.getAttribute('data-division-id');
                    const allRows = document.querySelectorAll('tr.division-row');
                    const descendants = Array.from(allRows).filter(r => {
                        return r.getAttribute('data-parent-id') === divisionId;
                    });
                    descendants.forEach(desc => hideRowAndDescendants(desc));
                    
                    // Update chevron state when hiding
                    const button = row.querySelector('.tree-toggle');
                    if (button) {
                        const chevronDown = button.querySelector('.chevron-down');
                        const chevronRight = button.querySelector('.chevron-right');
                        if (chevronDown) chevronDown.classList.add('hidden');
                        if (chevronRight) chevronRight.classList.remove('hidden');
                    }
                }

                // Initialize: all children are expanded by default
                document.addEventListener('DOMContentLoaded', function() {
                    // All children are visible by default, so chevrons should show down arrow
                    // This is already the default state from the HTML
                });
            </script>
            @endpush
        @else
            {{-- Flat View --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($divisions as $division)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $division->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $division->code ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($division->parent)
                                        {{ $division->parent->name }} @if($division->parent->code)({{ $division->parent->code }})@endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
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
                                    <form action="{{ route('divisions.destroy', $division) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this organization unit?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No organization units found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(isset($divisions) && $divisions->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $divisions->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
