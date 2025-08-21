<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API routes for Pathway Management
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('pathways', App\Http\Controllers\Api\PathwayController::class);
    Route::apiResource('steps', App\Http\Controllers\Api\PathwayStepController::class);
    Route::apiResource('cases', App\Http\Controllers\Api\PatientCaseController::class);
    Route::apiResource('case-details', App\Http\Controllers\Api\CaseDetailController::class);
    Route::apiResource('cost-references', App\Http\Controllers\Api\CostReferenceController::class);
    
    // Reporting APIs
    Route::get('reports/compliance', [App\Http\Controllers\Api\ReportController::class, 'compliance']);
    Route::get('reports/cost-variance', [App\Http\Controllers\Api\ReportController::class, 'costVariance']);
    Route::get('reports/pathway-performance', [App\Http\Controllers\Api\ReportController::class, 'pathwayPerformance']);
    
    // Dashboard APIs
    Route::get('dashboard/summary', [App\Http\Controllers\Api\DashboardController::class, 'summary']);
    Route::get('dashboard/trends', [App\Http\Controllers\Api\DashboardController::class, 'trends']);
});

// Public APIs (if any)
Route::get('pathways/public/{id}', [App\Http\Controllers\Api\PathwayController::class, 'publicShow']);
