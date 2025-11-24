<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientCaseController;
use App\Http\Controllers\PathwayController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CostReferenceController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\ReferenceController;
use Illuminate\Support\Facades\Route;

/*
--------------------------------------------------------------------------
| Web Routes
--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Hospital selection for Super Admin users
Route::get('/hospitals/select', [HospitalController::class, 'select'])->name('hospitals.select')->middleware(['auth', 'verified']);
Route::post('/hospitals/select', [HospitalController::class, 'setSelectedHospital'])->name('hospitals.select.set')->middleware(['auth', 'verified']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'set.hospital'])
    ->name('dashboard');

// Hospital management (superadmin only)
Route::resource('hospitals', HospitalController::class)
    ->middleware(['auth', 'verified', 'role:superadmin']);

Route::middleware(['auth', 'set.hospital'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('cases/template', [PatientCaseController::class, 'downloadTemplate'])->name('cases.template');

Route::middleware(['auth', 'set.hospital'])->group(function () {
    // Patient Cases
    Route::resource('cases', PatientCaseController::class);
    Route::get('cases/upload', [PatientCaseController::class, 'showUploadForm'])->name('cases.upload');
    Route::post('cases/upload', [PatientCaseController::class, 'upload'])->name('cases.upload.process');
    
    // Case Details management
    Route::get('cases/{case}/details/create', [PatientCaseController::class, 'createCaseDetail'])->name('cases.details.create');
    Route::post('cases/{case}/details', [PatientCaseController::class, 'storeCaseDetail'])->name('cases.details.store');
    Route::get('cases/{case}/details/{detail}/edit', [PatientCaseController::class, 'editCaseDetail'])->name('cases.details.edit');
    Route::put('cases/{case}/details/{detail}', [PatientCaseController::class, 'updateCaseDetail'])->name('cases.details.update');
    Route::delete('cases/{case}/details/{detail}', [PatientCaseController::class, 'deleteCaseDetail'])->name('cases.details.delete');
    // Copy all pathway steps into case details
    Route::post('cases/{case}/details/copy-steps', [PatientCaseController::class, 'copyPathwayStepsToCaseDetails'])->name('cases.details.copy-steps');

    // Clinical Pathways
    Route::resource('pathways', PathwayController::class);
    Route::get('pathways/{pathway}/builder', [PathwayController::class, 'builder'])->name('pathways.builder');
    Route::post('pathways/{pathway}/duplicate', [PathwayController::class, 'duplicate'])->name('pathways.duplicate');
    Route::post('pathways/{pathway}/version', [PathwayController::class, 'newVersion'])->name('pathways.version');
    Route::get('pathways/{pathway}/export-docx', [PathwayController::class, 'exportDocx'])->name('pathways.export-docx');
    Route::get('pathways/{pathway}/export-pdf', [PathwayController::class, 'exportPdf'])->name('pathways.export-pdf');
    
    // Pathway Steps (nested under pathways)
    Route::prefix('pathways/{pathway}')->group(function () {
        Route::post('steps', [\App\Http\Controllers\PathwayStepController::class, 'store'])->name('pathways.steps.store');
        Route::put('steps/{step}', [\App\Http\Controllers\PathwayStepController::class, 'update'])->name('pathways.steps.update');
        Route::delete('steps/{step}', [\App\Http\Controllers\PathwayStepController::class, 'destroy'])->name('pathways.steps.destroy');
        Route::post('steps/reorder', [\App\Http\Controllers\PathwayStepController::class, 'reorder'])->name('pathways.steps.reorder');
        // Bulk template download and import
        Route::get('steps/template', [\App\Http\Controllers\PathwayStepController::class, 'downloadTemplate'])->name('pathways.steps.template');
        Route::post('steps/import', [\App\Http\Controllers\PathwayStepController::class, 'import'])->name('pathways.steps.import');
    });
    
    // Cost References
    Route::get('cost-references/export', [CostReferenceController::class, 'export'])->name('cost-references.export');
    Route::delete('cost-references/bulk-destroy', [CostReferenceController::class, 'bulkDestroy'])->name('cost-references.bulk-destroy');
    Route::resource('cost-references', CostReferenceController::class);

    // Knowledge References
    Route::resource('references', ReferenceController::class);

    // Cost Centers
    Route::get('cost-centers/export', [App\Http\Controllers\CostCenterController::class, 'export'])->name('cost-centers.export');
    Route::resource('cost-centers', App\Http\Controllers\CostCenterController::class);

    // Expense Categories
    Route::get('expense-categories/export', [App\Http\Controllers\ExpenseCategoryController::class, 'export'])->name('expense-categories.export');
    Route::resource('expense-categories', App\Http\Controllers\ExpenseCategoryController::class);

    // Allocation Drivers
    Route::get('allocation-drivers/export', [App\Http\Controllers\AllocationDriverController::class, 'export'])->name('allocation-drivers.export');
    Route::resource('allocation-drivers', App\Http\Controllers\AllocationDriverController::class);

    // Tariff Classes
    Route::get('tariff-classes/export', [App\Http\Controllers\TariffClassController::class, 'export'])->name('tariff-classes.export');
    Route::resource('tariff-classes', App\Http\Controllers\TariffClassController::class);

    // GL Expenses
    Route::get('gl-expenses/import', [App\Http\Controllers\GlExpenseController::class, 'importForm'])->name('gl-expenses.import');
    Route::post('gl-expenses/import', [App\Http\Controllers\GlExpenseController::class, 'import'])->name('gl-expenses.import.process');
    Route::get('gl-expenses/export', [App\Http\Controllers\GlExpenseController::class, 'export'])->name('gl-expenses.export');
    Route::resource('gl-expenses', App\Http\Controllers\GlExpenseController::class);

    // Driver Statistics
    Route::get('driver-statistics/import', [App\Http\Controllers\DriverStatisticController::class, 'importForm'])->name('driver-statistics.import');
    Route::post('driver-statistics/import', [App\Http\Controllers\DriverStatisticController::class, 'import'])->name('driver-statistics.import.process');
    Route::get('driver-statistics/export', [App\Http\Controllers\DriverStatisticController::class, 'export'])->name('driver-statistics.export');
    Route::resource('driver-statistics', App\Http\Controllers\DriverStatisticController::class);

    // Service Volumes
    Route::get('service-volumes/import', [App\Http\Controllers\ServiceVolumeController::class, 'importForm'])->name('service-volumes.import');
    Route::post('service-volumes/import', [App\Http\Controllers\ServiceVolumeController::class, 'import'])->name('service-volumes.import.process');
    Route::get('service-volumes/export', [App\Http\Controllers\ServiceVolumeController::class, 'export'])->name('service-volumes.export');
    Route::resource('service-volumes', App\Http\Controllers\ServiceVolumeController::class);

    // JKN CBG Codes
    // Expose search and tariff lookups to all authenticated users
    Route::get('jkn-cbg-codes/search', [App\Http\Controllers\JknCbgCodeController::class, 'search'])->name('jkn-cbg-codes.search');
    Route::get('jkn-cbg-codes/tariff', [App\Http\Controllers\JknCbgCodeController::class, 'getTariff'])->name('jkn-cbg-codes.tariff');

    // Admin-only CRUD for managing the codes
    Route::middleware('role:admin')->group(function () {
        Route::resource('jkn-cbg-codes', App\Http\Controllers\JknCbgCodeController::class);
    });

    // Users (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('users/{user}/change-password', [UserController::class, 'changePasswordForm'])->name('users.change-password');
        Route::post('users/{user}/change-password', [UserController::class, 'changePassword'])->name('users.change-password.update');
    });

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');
    Route::get('reports/compliance', [ReportController::class, 'compliance'])->name('reports.compliance');
    Route::get('reports/cost-variance', [ReportController::class, 'costVariance'])->name('reports.cost-variance');
    Route::get('reports/pathway-performance', [ReportController::class, 'pathwayPerformance'])->name('reports.pathway-performance');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::post('reports/export/generate', [ReportController::class, 'generateExport'])->name('reports.export.generate');
    Route::get('reports/export/{export}/download', [ReportController::class, 'downloadExport'])->name('reports.export.download');
    Route::get('reports/export/pdf/{type}', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('reports/export/excel/{type}', [ReportController::class, 'exportExcel'])->name('reports.export.excel');

    // SIM RS Integration Routes
    Route::prefix('simrs')->group(function () {
        Route::get('/master-barang', [App\Http\Controllers\SimrsController::class, 'masterBarang'])->name('simrs.master-barang');
        Route::get('/tindakan-rawat-jalan', [App\Http\Controllers\SimrsController::class, 'tindakanRawatJalanView'])->name('simrs.tindakan-rawat-jalan');
        Route::get('/tindakan-rawat-inap', [App\Http\Controllers\SimrsController::class, 'tindakanRawatInapView'])->name('simrs.tindakan-rawat-inap');
        Route::get('/laboratorium', [App\Http\Controllers\SimrsController::class, 'laboratorium'])->name('simrs.laboratorium');
        Route::get('/radiologi', [App\Http\Controllers\SimrsController::class, 'radiologi'])->name('simrs.radiologi');
        Route::get('/operasi', [App\Http\Controllers\SimrsController::class, 'operasi'])->name('simrs.operasi');
        Route::get('/kamar', [App\Http\Controllers\SimrsController::class, 'kamarView'])->name('simrs.kamar');
        
        // SIMRS Sync Routes
        Route::get('/sync', [App\Http\Controllers\SimrsSyncController::class, 'index'])->name('simrs.sync');
        Route::post('/sync/drugs', [App\Http\Controllers\SimrsSyncController::class, 'syncDrugs'])->name('simrs.sync.drugs');
    });

    // Audit Logs (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('audit-logs', AuditLogController::class)->only(['index', 'show', 'destroy']);
        Route::delete('audit-logs', [AuditLogController::class, 'clear'])->name('audit-logs.clear');
    });
});

require __DIR__.'/auth.php';

