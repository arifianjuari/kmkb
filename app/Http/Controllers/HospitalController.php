<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hospitals = Hospital::all();
        return view('hospitals.index', compact('hospitals'));
    }
    
    /**
     * Show the hospital selection page for Super Admin users.
     */
    public function select()
    {
        $hospitals = Hospital::all();
        return view('hospitals.select', compact('hospitals'));
    }
    
    /**
     * Set the selected hospital for Super Admin users.
     */
    public function setSelectedHospital(Request $request)
    {
        $request->validate([
            'hospital_id' => 'required|exists:hospitals,id',
        ]);
        
        // Store the selected hospital ID in session
        session(['selected_hospital_id' => $request->hospital_id]);
        
        return redirect()->route('dashboard')
            ->with('status', 'Rumah sakit berhasil dipilih.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hospitals.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Handle potential PHP upload errors (e.g., file exceeds upload_max_filesize)
        $logoFile = $request->file('logo');
        if ($logoFile && $logoFile->getError() !== UPLOAD_ERR_OK) {
            return back()
                ->withErrors(['logo' => __('Gagal mengunggah logo. Kemungkinan ukuran file melebihi batas server (upload_max_filesize/post_max_size).')])
                ->withInput();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:hospitals,code',
            // Allow common image formats including SVG and WebP. Increase limit to 5 MB (5120 KB).
            'logo' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'theme_color' => 'nullable|string|regex:/^#[0-9a-fA-F]{6}$/i',
            'address' => 'nullable|string',
            'contact' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $request->only([
            'name',
            'code',
            'theme_color',
            'address',
            'contact',
            'is_active'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            try {
                $logo = $request->file('logo');
                $logoName = Str::slug($request->name) . '-' . time() . '.' . $logo->getClientOriginalExtension();
                // Ensure directory and store on public disk
                Storage::disk('public')->makeDirectory('hospitals');
                $logo->storeAs('hospitals', $logoName, 'public');
                $data['logo_path'] = 'hospitals/' . $logoName;
            } catch (\Throwable $e) {
                Log::error('Hospital logo upload failed on store()', [
                    'hospital_name' => $request->name,
                    'error' => $e->getMessage(),
                ]);
                return back()->withErrors(['logo' => __('Gagal menyimpan logo. Silakan coba lagi atau hubungi admin.')])->withInput();
            }
        }

        $data['is_active'] = $request->boolean('is_active');

        Hospital::create($data);

        return redirect()->route('hospitals.index')
            ->with('status', 'Rumah sakit berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Hospital $hospital)
    {
        return view('hospitals.show', compact('hospital'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hospital $hospital)
    {
        return view('hospitals.edit', compact('hospital'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hospital $hospital)
    {
        // Handle potential PHP upload errors (e.g., file exceeds upload_max_filesize)
        $logoFile = $request->file('logo');
        if ($logoFile && $logoFile->getError() !== UPLOAD_ERR_OK) {
            return back()
                ->withErrors(['logo' => __('Gagal mengunggah logo. Kemungkinan ukuran file melebihi batas server (upload_max_filesize/post_max_size).')])
                ->withInput();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:hospitals,code,' . $hospital->id,
            // Allow common image formats including SVG and WebP. Increase limit to 5 MB (5120 KB).
            'logo' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'theme_color' => 'nullable|string|regex:/^#[0-9a-fA-F]{6}$/i',
            'address' => 'nullable|string',
            'contact' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $request->only([
            'name',
            'code',
            'theme_color',
            'address',
            'contact',
            'is_active'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            try {
                // Delete old logo if exists
                if ($hospital->logo_path && Storage::disk('public')->exists($hospital->logo_path)) {
                    Storage::disk('public')->delete($hospital->logo_path);
                }

                $logo = $request->file('logo');
                $logoName = Str::slug($request->name) . '-' . time() . '.' . $logo->getClientOriginalExtension();
                Storage::disk('public')->makeDirectory('hospitals');
                $logo->storeAs('hospitals', $logoName, 'public');
                $data['logo_path'] = 'hospitals/' . $logoName;
            } catch (\Throwable $e) {
                Log::error('Hospital logo upload failed on update()', [
                    'hospital_id' => $hospital->id,
                    'error' => $e->getMessage(),
                ]);
                return back()->withErrors(['logo' => __('Gagal menyimpan logo. Silakan coba lagi atau hubungi admin.')])->withInput();
            }
        }

        $data['is_active'] = $request->boolean('is_active');

        $hospital->update($data);

        return redirect()->route('hospitals.index')
            ->with('status', 'Rumah sakit berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hospital $hospital)
    {
        // Delete logo if exists
        if ($hospital->logo_path && Storage::disk('public')->exists($hospital->logo_path)) {
            Storage::disk('public')->delete($hospital->logo_path);
        }

        $hospital->delete();

        return redirect()->route('hospitals.index')
            ->with('status', 'Rumah sakit berhasil dihapus.');
    }
}
