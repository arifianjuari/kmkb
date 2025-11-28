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
use App\Http\Controllers\MigrateStorageController;
use App\Http\Controllers\UploadProxyController;
use Illuminate\Support\Facades\Route;
Route::get('/uploads/{path}', UploadProxyController::class)
    ->where('path', '.*')
    ->name('uploads.proxy');


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
    // Update case annotation
    Route::put('cases/{case}/annotation', [PatientCaseController::class, 'updateAnnotation'])->name('cases.annotation.update');

    // Clinical Pathways
    Route::resource('pathways', PathwayController::class);
    Route::get('pathways/{pathway}/builder', [PathwayController::class, 'builder'])->name('pathways.builder');
    Route::post('pathways/{pathway}/duplicate', [PathwayController::class, 'duplicate'])->name('pathways.duplicate');
    Route::post('pathways/{pathway}/version', [PathwayController::class, 'newVersion'])->name('pathways.version');
    Route::get('pathways/{pathway}/export-docx', [PathwayController::class, 'exportDocx'])->name('pathways.export-docx');
    Route::get('pathways/{pathway}/export-pdf', [PathwayController::class, 'exportPdf'])->name('pathways.export-pdf');
    Route::post('pathways/{pathway}/recalculate-summary', [PathwayController::class, 'recalculateSummary'])->name('pathways.recalculate-summary');
    
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
    Route::get('cost-references/template', [CostReferenceController::class, 'downloadTemplate'])->name('cost-references.template');
    Route::post('cost-references/import', [CostReferenceController::class, 'import'])->name('cost-references.import');
    Route::delete('cost-references/bulk-destroy', [CostReferenceController::class, 'bulkDestroy'])->name('cost-references.bulk-destroy');
    Route::get('cost-references/search', [CostReferenceController::class, 'search'])->name('cost-references.search');
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

    // Allocation Maps
    Route::resource('allocation-maps', App\Http\Controllers\AllocationMapController::class);

    // Run Allocation
    Route::get('allocation/run', [App\Http\Controllers\AllocationController::class, 'runForm'])->name('allocation.run.form');
    Route::post('allocation/run', [App\Http\Controllers\AllocationController::class, 'run'])->name('allocation.run');

    // Allocation Results
    Route::get('allocation-results/export', [App\Http\Controllers\AllocationResultController::class, 'export'])->name('allocation-results.export');
    Route::resource('allocation-results', App\Http\Controllers\AllocationResultController::class);

    // Tariff Simulation
    Route::get('tariff-simulation', [App\Http\Controllers\TariffSimulationController::class, 'index'])->name('tariff-simulation.index');
    Route::post('tariff-simulation/simulate', [App\Http\Controllers\TariffSimulationController::class, 'simulate'])->name('tariff-simulation.simulate');
    Route::get('tariff-simulation/preview', [App\Http\Controllers\TariffSimulationController::class, 'preview'])->name('tariff-simulation.preview');
    Route::get('tariff-simulation/export', [App\Http\Controllers\TariffSimulationController::class, 'export'])->name('tariff-simulation.export');

    // Final Tariffs
    Route::get('final-tariffs/export', [App\Http\Controllers\FinalTariffController::class, 'export'])->name('final-tariffs.export');
    Route::resource('final-tariffs', App\Http\Controllers\FinalTariffController::class);

    // Tariff Explorer
    Route::get('tariff-explorer', [App\Http\Controllers\TariffExplorerController::class, 'index'])->name('tariff-explorer.index');
    Route::get('tariff-explorer/{finalTariff}', [App\Http\Controllers\TariffExplorerController::class, 'show'])->name('tariff-explorer.show');
    Route::get('tariff-explorer/compare/{serviceId}', [App\Http\Controllers\TariffExplorerController::class, 'compare'])->name('tariff-explorer.compare');

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

    // Service Volume (Current) placeholder routes
    Route::prefix('service-volume-current')->group(function () {
        Route::get('/master-barang', [App\Http\Controllers\ServiceVolumeCurrentController::class, 'masterBarang'])->name('svc-current.master-barang');
        Route::get('/tindakan-rawat-jalan', [App\Http\Controllers\ServiceVolumeCurrentController::class, 'tindakanRawatJalan'])->name('svc-current.tindakan-rawat-jalan');
        Route::get('/tindakan-rawat-jalan/export', [App\Http\Controllers\ServiceVolumeCurrentController::class, 'exportTindakanRawatJalan'])->name('svc-current.tindakan-rawat-jalan.export');
        Route::get('/tindakan-rawat-inap', [App\Http\Controllers\ServiceVolumeCurrentController::class, 'tindakanRawatInap'])->name('svc-current.tindakan-rawat-inap');
        Route::get('/tindakan-rawat-inap/export', [App\Http\Controllers\ServiceVolumeCurrentController::class, 'exportTindakanRawatInap'])->name('svc-current.tindakan-rawat-inap.export');
        Route::get('/laboratorium', [App\Http\Controllers\ServiceVolumeCurrentController::class, 'laboratorium'])->name('svc-current.laboratorium');
        Route::get('/laboratorium/export', [App\Http\Controllers\ServiceVolumeCurrentController::class, 'exportLaboratorium'])->name('svc-current.laboratorium.export');
        Route::get('/radiologi', [App\Http\Controllers\ServiceVolumeCurrentController::class, 'radiologi'])->name('svc-current.radiologi');
        Route::get('/radiologi/export', [App\Http\Controllers\ServiceVolumeCurrentController::class, 'exportRadiologi'])->name('svc-current.radiologi.export');
        Route::get('/operasi', [App\Http\Controllers\ServiceVolumeCurrentController::class, 'operasi'])->name('svc-current.operasi');
        Route::get('/operasi/export', [App\Http\Controllers\ServiceVolumeCurrentController::class, 'exportOperasi'])->name('svc-current.operasi.export');
        Route::get('/kamar', [App\Http\Controllers\ServiceVolumeCurrentController::class, 'kamar'])->name('svc-current.kamar');
        Route::get('/sync', [App\Http\Controllers\ServiceVolumeCurrentController::class, 'sync'])->name('svc-current.sync');
    });

    // Audit Logs (admin and observer can view, admin only can delete)
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    
    // Audit Logs delete/clear (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::delete('audit-logs/{auditLog}', [AuditLogController::class, 'destroy'])->name('audit-logs.destroy');
        Route::delete('audit-logs', [AuditLogController::class, 'clear'])->name('audit-logs.clear');
        
        // Migration routes (temporary - remove after migration is complete)
        Route::get('migrate-storage', [MigrateStorageController::class, 'index'])->name('migrate-storage.index');
        Route::post('migrate-storage', [MigrateStorageController::class, 'migrate'])->name('migrate-storage');
    });

    // ============================================
    // NEW MENU STRUCTURE ROUTES (Revised Structure)
    // ============================================

    // Setup Routes
    Route::prefix('setup')->group(function () {
        // Costing Setup
        Route::prefix('costing')->group(function () {
            Route::get('cost-centers', function () { return redirect()->route('cost-centers.index'); })->name('setup.costing.cost-centers');
            Route::get('expense-categories', function () { return redirect()->route('expense-categories.index'); })->name('setup.costing.expense-categories');
            Route::get('allocation-drivers', function () { return redirect()->route('allocation-drivers.index'); })->name('setup.costing.allocation-drivers');
            Route::get('tariff-classes', function () { return redirect()->route('tariff-classes.index'); })->name('setup.costing.tariff-classes');
        });

        // Service Catalog
        Route::prefix('service-catalog')->group(function () {
            Route::get('service-items', function () { return redirect()->route('cost-references.index'); })->name('setup.service-catalog.service-items');
            Route::get('simrs-linked', [App\Http\Controllers\Setup\ServiceCatalogController::class, 'simrsLinked'])->name('setup.service-catalog.simrs-linked');
            Route::get('import-export', [App\Http\Controllers\Setup\ServiceCatalogController::class, 'importExport'])->name('setup.service-catalog.import-export');
        });

        // JKN / INA-CBG Codes
        Route::prefix('jkn-cbg-codes')->group(function () {
            Route::get('list', function () { return redirect()->route('jkn-cbg-codes.index'); })->name('setup.jkn-cbg-codes.list');
            Route::get('base-tariff', [App\Http\Controllers\JknCbgCodeController::class, 'baseTariff'])->name('setup.jkn-cbg-codes.base-tariff');
        });

        // SIMRS Integration
        Route::prefix('simrs-integration')->group(function () {
            Route::get('settings', [App\Http\Controllers\Setup\SimrsIntegrationController::class, 'settings'])->name('setup.simrs-integration.settings');
            Route::get('data-sources', function () { return redirect()->route('simrs.master-barang'); })->name('setup.simrs-integration.data-sources');
            Route::get('sync', function () { return redirect()->route('simrs.sync'); })->name('setup.simrs-integration.sync');
        });
    });

    // Data Input Routes
    Route::prefix('data-input')->group(function () {
        Route::get('gl-expenses', function () { return redirect()->route('gl-expenses.index'); })->name('data-input.gl-expenses');
        Route::get('driver-statistics', function () { return redirect()->route('driver-statistics.index'); })->name('data-input.driver-statistics');
        Route::get('service-volumes', function () { return redirect()->route('service-volumes.index'); })->name('data-input.service-volumes');
        Route::get('import-center', [App\Http\Controllers\DataInput\ImportCenterController::class, 'index'])->name('data-input.import-center');
    });

    // Costing Process Routes
    Route::prefix('costing-process')->group(function () {
        // Pre-Allocation Check
        Route::prefix('pre-allocation-check')->group(function () {
            Route::get('gl-completeness', [App\Http\Controllers\CostingProcess\PreAllocationCheckController::class, 'glCompleteness'])->name('costing-process.pre-allocation-check.gl-completeness');
            Route::get('driver-completeness', [App\Http\Controllers\CostingProcess\PreAllocationCheckController::class, 'driverCompleteness'])->name('costing-process.pre-allocation-check.driver-completeness');
            Route::get('service-volume-completeness', [App\Http\Controllers\CostingProcess\PreAllocationCheckController::class, 'serviceVolumeCompleteness'])->name('costing-process.pre-allocation-check.service-volume-completeness');
            Route::get('mapping-validation', [App\Http\Controllers\CostingProcess\PreAllocationCheckController::class, 'mappingValidation'])->name('costing-process.pre-allocation-check.mapping-validation');
        });

        // Allocation Engine
        Route::prefix('allocation')->group(function () {
            Route::get('maps', function () { return redirect()->route('allocation-maps.index'); })->name('costing-process.allocation.maps');
            Route::get('run', function () { return redirect()->route('allocation.run.form'); })->name('costing-process.allocation.run');
            Route::get('results', function () { return redirect()->route('allocation-results.index'); })->name('costing-process.allocation.results');
        });

        // Unit Cost Engine
        Route::prefix('unit-cost')->group(function () {
            Route::get('calculate', [App\Http\Controllers\CostingProcess\UnitCostController::class, 'calculate'])->name('costing-process.unit-cost.calculate');
            Route::get('results', [App\Http\Controllers\CostingProcess\UnitCostController::class, 'results'])->name('costing-process.unit-cost.results');
            Route::get('compare', [App\Http\Controllers\CostingProcess\UnitCostController::class, 'compare'])->name('costing-process.unit-cost.compare');
        });
    });

    // Tariff Management Routes
    Route::prefix('tariffs')->group(function () {
        Route::get('simulation', function () { return redirect()->route('tariff-simulation.index'); })->name('tariffs.simulation');
        Route::get('structure', [App\Http\Controllers\Tariff\TariffStructureController::class, 'index'])->name('tariffs.structure');
        Route::get('final', function () { return redirect()->route('final-tariffs.index'); })->name('tariffs.final');
        Route::get('explorer', function () { return redirect()->route('tariff-explorer.index'); })->name('tariffs.explorer');
        Route::get('comparison', [App\Http\Controllers\Tariff\TariffComparisonController::class, 'index'])->name('tariffs.comparison');
    });

    // Clinical Pathways Additional Routes
    Route::prefix('pathways')->group(function () {
        Route::get('{pathway}/summary', [PathwayController::class, 'summary'])->name('pathways.summary');
        Route::get('{pathway}/approval', [App\Http\Controllers\Pathway\PathwayApprovalController::class, 'show'])->name('pathways.approval');
        Route::get('templates', [App\Http\Controllers\Pathway\PathwayTemplateController::class, 'index'])->name('pathways.templates');
    });

    // Patient Cases Additional Routes
    Route::prefix('cases')->group(function () {
        Route::get('{case}/costing', [App\Http\Controllers\PatientCase\CaseCostingController::class, 'show'])->name('cases.costing');
        Route::get('{case}/variance', function ($case) { return redirect()->route('cases.show', $case); })->name('cases.variance');
    });

    // Analytics Routes
    Route::prefix('analytics')->group(function () {
        Route::get('cost-center-performance', [App\Http\Controllers\Analytics\CostCenterPerformanceController::class, 'index'])->name('analytics.cost-center-performance');
        Route::get('allocation-summary', [App\Http\Controllers\Analytics\AllocationSummaryController::class, 'index'])->name('analytics.allocation-summary');
        Route::get('unit-cost-summary', [App\Http\Controllers\Analytics\UnitCostSummaryController::class, 'index'])->name('analytics.unit-cost-summary');
        Route::get('tariff-analytics', [App\Http\Controllers\Analytics\TariffAnalyticsController::class, 'index'])->name('analytics.tariff-analytics');
        Route::get('pathway-compliance', function () { return redirect()->route('reports.compliance'); })->name('analytics.pathway-compliance');
        Route::get('case-variance', function () { return redirect()->route('reports.cost-variance'); })->name('analytics.case-variance');
        Route::get('los-analysis', function () { return redirect()->route('reports.pathway-performance'); })->name('analytics.los-analysis');
        Route::get('continuous-improvement', [App\Http\Controllers\Analytics\ContinuousImprovementController::class, 'index'])->name('analytics.continuous-improvement');
    });
});

// ============================================
// BACKWARD COMPATIBILITY REDIRECTS
// ============================================
Route::middleware(['auth', 'set.hospital'])->group(function () {
    // Master Data redirects (if any direct routes exist)
    Route::get('master-data/{any}', function ($any) {
        return redirect()->route("setup.costing.{$any}");
    })->where('any', '.*');

    // GL & Expenses redirects
    Route::get('gl-expenses/{any?}', function ($any = null) {
        if ($any) {
            return redirect()->route("gl-expenses.{$any}");
        }
        return redirect()->route('data-input.gl-expenses');
    })->where('any', '.*');

    // Allocation redirects
    Route::get('allocation/{any?}', function ($any = null) {
        if ($any === 'run') {
            return redirect()->route('costing-process.allocation.run');
        }
        return redirect()->route('costing-process.allocation.maps');
    })->where('any', '.*');

    // Unit Cost redirects
    Route::get('unit-cost/{any?}', function ($any = null) {
        if ($any === 'calculate') {
            return redirect()->route('costing-process.unit-cost.calculate');
        } elseif ($any === 'results') {
            return redirect()->route('costing-process.unit-cost.results');
        }
        return redirect()->route('costing-process.unit-cost.calculate');
    })->where('any', '.*');

    // Reports redirects
    Route::get('reports/{any?}', function ($any = null) {
        if ($any === 'compliance') {
            return redirect()->route('analytics.pathway-compliance');
        } elseif ($any === 'cost-variance') {
            return redirect()->route('analytics.case-variance');
        } elseif ($any === 'pathway-performance') {
            return redirect()->route('analytics.los-analysis');
        }
        return redirect()->route('analytics.pathway-compliance');
    })->where('any', '.*');

    // SIMRS redirects
    Route::get('simrs/{any?}', function ($any = null) {
        if ($any === 'sync') {
            return redirect()->route('setup.simrs-integration.sync');
        }
        return redirect()->route('setup.simrs-integration.data-sources');
    })->where('any', '.*');
});

require __DIR__.'/auth.php';

