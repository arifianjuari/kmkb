<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetHospital
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the authenticated user
        $user = auth()->user();
        
        // If user is a superadmin
        if ($user && $user->hasRole('superadmin')) {
            // Check if a hospital context has been manually selected
            $selectedHospitalId = session('selected_hospital_id');
            
            // If no hospital context is selected and this is not an API request for hospital selection
            if (!$selectedHospitalId && !$request->is('api/hospitals/select')) {
                // For API requests to SIMRS endpoints, return an error
                if ($request->is('api/simrs/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Super admin must select a hospital context before accessing SIMRS data'
                    ], 400);
                }
                
                // For web requests, redirect to hospital selection page
                if (!$request->is('hospitals/select')) {
                    return redirect()->route('hospitals.select');
                }
            }
            
            // Set the selected hospital_id in session
            if ($selectedHospitalId) {
                session(['hospital_id' => $selectedHospitalId]);
            }
            
            return $next($request);
        }
        
        // If user is authenticated and has a hospital_id, set it in the session
        if ($user && $user->hospital_id) {
            session(['hospital_id' => $user->hospital_id]);
        }
        
        return $next($request);
    }
}
