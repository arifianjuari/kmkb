<?php

namespace App\Http\Controllers;

use App\Models\ClinicalPathway;
use App\Models\PathwayStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PathwayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pathways = ClinicalPathway::with('creator')->latest()->paginate(10);
        return view('pathways.index', compact('pathways'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pathways.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'diagnosis_code' => 'required|string|max:50',
            'version' => 'required|string|max:20',
            'effective_date' => 'required|date',
            'status' => 'required|in:draft,active,inactive',
        ]);

        DB::beginTransaction();
        try {
            $pathway = new ClinicalPathway();
            $pathway->name = $request->name;
            $pathway->description = $request->description;
            $pathway->diagnosis_code = $request->diagnosis_code;
            $pathway->version = $request->version;
            $pathway->effective_date = $request->effective_date;
            $pathway->status = $request->status;
            $pathway->created_by = Auth::id();
            $pathway->save();

            DB::commit();
            
            return redirect()->route('pathways.index')
                ->with('success', 'Clinical pathway created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to create clinical pathway: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function show(ClinicalPathway $pathway)
    {
        $pathway->load(['steps', 'creator']);
        return view('pathways.show', compact('pathway'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function edit(ClinicalPathway $pathway)
    {
        return view('pathways.edit', compact('pathway'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClinicalPathway $pathway)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'diagnosis_code' => 'required|string|max:50',
            'version' => 'required|string|max:20',
            'effective_date' => 'required|date',
            'status' => 'required|in:draft,active,inactive',
        ]);

        $pathway->update($request->all());

        return redirect()->route('pathways.index')
            ->with('success', 'Clinical pathway updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClinicalPathway $pathway)
    {
        $pathway->delete();

        return redirect()->route('pathways.index')
            ->with('success', 'Clinical pathway deleted successfully.');
    }

    /**
     * Show the pathway builder interface.
     *
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function builder(ClinicalPathway $pathway)
    {
        $pathway->load('steps');
        return view('pathways.builder', compact('pathway'));
    }
}
