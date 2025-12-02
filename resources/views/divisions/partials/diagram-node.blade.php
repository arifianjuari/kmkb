@php
    $children = $allDivisions->where('parent_id', $division->id)->sortBy('name');
    $hasChildren = $children->count() > 0;
    $inactiveClass = !$division->is_active ? 'inactive' : '';
    $hasChildrenClass = $hasChildren ? 'has-children' : '';
    $isRoot = $level === 0;
@endphp

<div class="org-node {{ $hasChildrenClass }}">
    @if($hasChildren && !$isRoot)
        <div class="org-connector-vertical"></div>
    @endif
    
    <div class="org-node-box {{ $inactiveClass }}" 
         title="{{ $division->description ?? $division->name }}">
        <div style="font-weight: 600; line-height: 1.4;">{{ $division->name }}</div>
    </div>
    
    @if($hasChildren)
        <div class="org-node-children">
            @if($children->count() > 1)
                <div class="org-connector-horizontal"></div>
            @endif
            <div class="org-connector-vertical-up"></div>
            @foreach($children as $child)
                @include('divisions.partials.diagram-node', ['division' => $child, 'allDivisions' => $allDivisions, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>

