<?php

namespace App\Http\Middleware;

use App\Services\HospitalSimrsConnection;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSimrsDatabase
{
    public function __construct(
        private HospitalSimrsConnection $simrsConnection
    ) {}

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/simrs/test-connection')) {
            return $next($request);
        }

        if (! $this->simrsConnection->isConfigured()) {
            return response()->json([
                'success' => false,
                'configured' => false,
                'available' => false,
                'message' => $this->simrsConnection->configurationMessage(),
            ], 503);
        }

        if (! $this->simrsConnection->isAvailable()) {
            return response()->json([
                'success' => false,
                'configured' => true,
                'available' => false,
                'message' => $this->simrsConnection->availabilityMessage(),
            ], 503);
        }

        return $next($request);
    }
}
