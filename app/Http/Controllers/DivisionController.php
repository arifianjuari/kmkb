<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\BlocksObserver;
use App\Models\Division;

class DivisionController extends Controller
{
    use BlocksObserver;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $isActive = $request->get('is_active');
        $viewMode = $request->get('view_mode', 'tree'); // 'tree', 'flat', or 'diagram'

        if ($viewMode === 'diagram') {
            // Get all divisions for diagram view
            $allDivisions = Division::where('hospital_id', hospital('id'))
                ->with(['parent', 'children'])
                ->get();

            // Apply filters if provided
            if ($search || ($isActive !== null && $isActive !== '')) {
                $filteredIds = collect();
                
                $matchingDivisions = $allDivisions->filter(function($division) use ($search, $isActive) {
                    $matchesSearch = true;
                    $matchesActive = true;
                    
                    if ($search) {
                        $matchesSearch = stripos($division->name, $search) !== false 
                            || stripos($division->code ?? '', $search) !== false;
                    }
                    
                    if ($isActive !== null && $isActive !== '') {
                        $matchesActive = $division->is_active == $isActive;
                    }
                    
                    return $matchesSearch && $matchesActive;
                });
                
                $matchingDivisions->each(function($division) use (&$filteredIds, $allDivisions) {
                    $filteredIds->push($division->id);
                    $current = $division;
                    while ($current->parent_id) {
                        $filteredIds->push($current->parent_id);
                        $current = $allDivisions->firstWhere('id', $current->parent_id);
                        if (!$current) break;
                    }
                });
                
                $allDivisions = $allDivisions->filter(function($division) use ($filteredIds) {
                    return $filteredIds->contains($division->id);
                });
            }

            $rootDivisions = $allDivisions->whereNull('parent_id')->sortBy('name')->values();
            
            return view('divisions.index', compact('rootDivisions', 'allDivisions', 'search', 'isActive', 'viewMode'));
        } elseif ($viewMode === 'tree') {
            // Get all divisions for tree view
            $allDivisions = Division::where('hospital_id', hospital('id'))
                ->with(['parent', 'children'])
                ->get();

            // Apply filters if provided - include parents of matching children
            if ($search || ($isActive !== null && $isActive !== '')) {
                $filteredIds = collect();
                
                // First, find all divisions that match the filter
                $matchingDivisions = $allDivisions->filter(function($division) use ($search, $isActive) {
                    $matchesSearch = true;
                    $matchesActive = true;
                    
                    if ($search) {
                        $matchesSearch = stripos($division->name, $search) !== false 
                            || stripos($division->code ?? '', $search) !== false;
                    }
                    
                    if ($isActive !== null && $isActive !== '') {
                        $matchesActive = $division->is_active == $isActive;
                    }
                    
                    return $matchesSearch && $matchesActive;
                });
                
                // Collect IDs of matching divisions and their parents
                $matchingDivisions->each(function($division) use (&$filteredIds, $allDivisions) {
                    $filteredIds->push($division->id);
                    // Include all ancestors
                    $current = $division;
                    while ($current->parent_id) {
                        $filteredIds->push($current->parent_id);
                        $current = $allDivisions->firstWhere('id', $current->parent_id);
                        if (!$current) break;
                    }
                });
                
                // Filter allDivisions to include only matching divisions and their parents
                $allDivisions = $allDivisions->filter(function($division) use ($filteredIds) {
                    return $filteredIds->contains($division->id);
                });
            }

            // Get root divisions (no parent) and sort
            $rootDivisions = $allDivisions->whereNull('parent_id')->sortBy('name')->values();
            
            return view('divisions.index', compact('rootDivisions', 'allDivisions', 'search', 'isActive', 'viewMode'));
        } else {
            // Flat view with pagination
            $query = Division::where('hospital_id', hospital('id'))->with('parent');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%");
                });
            }

            if ($isActive !== null && $isActive !== '') {
                $query->where('is_active', $isActive);
            }

            $divisions = $query->latest()->paginate(15)->appends($request->query());

            return view('divisions.index', compact('divisions', 'search', 'isActive', 'viewMode'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->blockObserver('membuat');
        $parentDivisions = Division::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('divisions.create', compact('parentDivisions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->blockObserver('membuat');
        
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:divisions,id',
            'is_active' => 'boolean',
        ]);

        // Validate parent belongs to same hospital
        if ($request->filled('parent_id')) {
            $parent = Division::where('id', $request->parent_id)
                ->where('hospital_id', hospital('id'))
                ->first();
            if (!$parent) {
                return back()->withErrors(['parent_id' => 'Parent division tidak valid.'])->withInput();
            }
        }

        Division::create(array_merge($validated, [
            'hospital_id' => hospital('id'),
            'parent_id' => $request->parent_id ?: null,
            'is_active' => $request->has('is_active') ? true : false,
        ]));

        return redirect()->route('divisions.index')
            ->with('success', 'Division berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // This method was not part of the instruction, keeping it as is or removing if implied.
        // Based on the instruction, only index, create, store, edit, update, destroy are to be implemented.
        // Since it's not explicitly removed, I'll keep it as is.
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Division $division)
    {
        $this->blockObserver('mengubah');

        if ($division->hospital_id !== hospital('id')) {
            abort(404);
        }

        $parentDivisions = Division::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->where('id', '!=', $division->id) // Exclude current division from parent options
            ->orderBy('name')
            ->get();

        return view('divisions.edit', compact('division', 'parentDivisions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Division $division)
    {
        $this->blockObserver('mengubah');

        if ($division->hospital_id !== hospital('id')) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:divisions,id',
            'is_active' => 'boolean',
        ]);

        // Validate parent belongs to same hospital and not the division itself
        if ($request->filled('parent_id')) {
            if ($request->parent_id === $division->id) {
                return back()->withErrors(['parent_id' => 'Division tidak dapat menjadi parent dirinya sendiri.'])->withInput();
            }
            $parent = Division::where('id', $request->parent_id)
                ->where('hospital_id', hospital('id'))
                ->first();
            if (!$parent) {
                return back()->withErrors(['parent_id' => 'Parent division tidak valid.'])->withInput();
            }
        }

        $division->update(array_merge($validated, [
            'parent_id' => $request->parent_id ?: null,
            'is_active' => $request->has('is_active') ? true : false,
        ]));

        return redirect()->route('divisions.index')
            ->with('success', 'Division berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Division $division)
    {
        $this->blockObserver('menghapus');

        if ($division->hospital_id !== hospital('id')) {
            abort(404);
        }

        // Check usage if necessary (e.g. in Cost Centers)
        // For now, simple delete
        $division->delete();

        return redirect()->route('divisions.index')
            ->with('success', 'Division berhasil dihapus.');
    }
}
