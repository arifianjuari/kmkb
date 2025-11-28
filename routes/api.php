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
    
    Route::middleware(['auth:sanctum', 'set.hospital'])->group(function () {
        // SIM RS Integration APIs
        Route::get('simrs/test-connection', [App\Http\Controllers\Api\SimrsController::class, 'testConnection']);
        Route::get('simrs/master-barang', [App\Http\Controllers\Api\SimrsController::class, 'masterBarang']);
        Route::get('simrs/tindakan-rawat-jalan', [App\Http\Controllers\Api\SimrsController::class, 'tindakanRawatJalan']);
        Route::get('simrs/tindakan-rawat-inap', [App\Http\Controllers\Api\SimrsController::class, 'tindakanRawatInap']);
        Route::get('simrs/laboratorium', [App\Http\Controllers\Api\SimrsController::class, 'laboratorium']);
        Route::get('simrs/radiologi', [App\Http\Controllers\Api\SimrsController::class, 'radiologi']);
        Route::get('simrs/radiologi/jenis', [App\Http\Controllers\Api\SimrsController::class, 'jenisRadiologi']);
        Route::get('simrs/operasi', [App\Http\Controllers\Api\SimrsController::class, 'operasi']);
        Route::get('simrs/kamar', [App\Http\Controllers\Api\SimrsController::class, 'kamar']);
        Route::get('simrs/all-data', [App\Http\Controllers\Api\SimrsController::class, 'allData']);
        Route::post('simrs/sync-master-barang', [App\Http\Controllers\Api\SimrsController::class, 'syncMasterBarang']);
        Route::post('simrs/sync-tindakan-rawat-jalan', [App\Http\Controllers\Api\SimrsController::class, 'syncTindakanRawatJalan']);
        Route::post('simrs/sync-tindakan-rawat-inap', [App\Http\Controllers\Api\SimrsController::class, 'syncTindakanRawatInap']);
        Route::post('simrs/sync-laboratorium', [App\Http\Controllers\Api\SimrsController::class, 'syncLaboratorium']);
        Route::post('simrs/sync-radiologi', [App\Http\Controllers\Api\SimrsController::class, 'syncRadiologi']);
        Route::post('simrs/sync-operasi', [App\Http\Controllers\Api\SimrsController::class, 'syncOperasi']);
        Route::post('simrs/sync-kamar', [App\Http\Controllers\Api\SimrsController::class, 'syncKamar']);
    });
    
    // Reporting APIs
    Route::get('reports/compliance', [App\Http\Controllers\Api\ReportController::class, 'compliance']);
    Route::get('reports/cost-variance', [App\Http\Controllers\Api\ReportController::class, 'costVariance']);
    Route::get('reports/pathway-performance', [App\Http\Controllers\Api\ReportController::class, 'pathwayPerformance']);
    
    // Dashboard APIs
    Route::get('dashboard/summary', [App\Http\Controllers\Api\DashboardController::class, 'summary']);
    Route::get('dashboard/trends', [App\Http\Controllers\Api\DashboardController::class, 'trends']);
    
    Route::middleware('set.hospital')->group(function () {
        Route::get('dashboard/overview', [App\Http\Controllers\Api\DashboardController::class, 'overview']);
        Route::get('dashboard/biaya-tarif', [App\Http\Controllers\Api\DashboardController::class, 'biayaTarif']);
        Route::get('dashboard/pathway-mutu', [App\Http\Controllers\Api\DashboardController::class, 'pathwayMutu']);
        Route::get('dashboard/variance-jkn', [App\Http\Controllers\Api\DashboardController::class, 'varianceJkn']);
        Route::get('dashboard/data-proses', [App\Http\Controllers\Api\DashboardController::class, 'dataProses']);
    });
    
    // Unit Cost APIs
    Route::get('unit-cost', [App\Http\Controllers\Api\UnitCostController::class, 'getUnitCost']);
    Route::get('unit-cost/versions', [App\Http\Controllers\Api\UnitCostController::class, 'getVersions']);
    Route::get('unit-costs', [App\Http\Controllers\Api\UnitCostController::class, 'getByCostReference']);
    Route::get('unit-costs/{id}', [App\Http\Controllers\Api\UnitCostController::class, 'show']);
});

// Public APIs (if any)
Route::get('pathways/public/{id}', [App\Http\Controllers\Api\PathwayController::class, 'publicShow']);
