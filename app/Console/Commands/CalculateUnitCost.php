<?php

namespace App\Console\Commands;

use App\Services\UnitCostCalculationService;
use App\Models\Hospital;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CalculateUnitCost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unit-cost:calculate 
                            {--hospital= : Hospital ID (optional, uses first hospital if not provided)}
                            {--year= : Year (required)}
                            {--month= : Month 1-12 (required)}
                            {--vlabel= : Version label (required, e.g. UC_2025_JAN)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate unit cost for a specific period and version';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get and validate options
        $hospitalId = $this->option('hospital');
        $year = $this->option('year');
        $month = $this->option('month');
        $version = $this->option('vlabel');

        // Validate inputs
        $validator = Validator::make([
            'year' => $year,
            'month' => $month,
            'vlabel' => $version,
        ], [
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'vlabel' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('  - ' . $error);
            }
            return 1;
        }

        // Get hospital
        if ($hospitalId) {
            $hospital = Hospital::find($hospitalId);
            if (!$hospital) {
                $this->error("Hospital with ID {$hospitalId} not found.");
                return 1;
            }
        } else {
            $hospital = Hospital::first();
            if (!$hospital) {
                $this->error('No hospital found. Please run HospitalsTableSeeder first.');
                return 1;
            }
            $this->info("Using hospital: {$hospital->name} (ID: {$hospital->id})");
        }

        $this->info('');
        $this->info('========================================');
        $this->info('Unit Cost Calculation');
        $this->info('========================================');
        $this->info("Hospital: {$hospital->name}");
        $this->info("Period: {$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT));
        $this->info("Version: {$version}");
        $this->info('');

        // Confirm before proceeding
        if (!$this->confirm('Do you want to proceed with the calculation?', true)) {
            $this->info('Calculation cancelled.');
            return 0;
        }

        $this->info('');
        $this->info('Starting calculation...');
        $this->info('');

        // Run calculation
        $service = new UnitCostCalculationService();
        $results = $service->calculateUnitCost(
            $hospital->id,
            (int) $year,
            (int) $month,
            $version
        );

        // Display results
        $this->info('');
        if ($results['success']) {
            $this->info('✓ Calculation completed successfully!');
            $this->info("  - Processed: {$results['processed']} cost references");
            
            if (!empty($results['warnings'])) {
                $this->warn('Warnings:');
                foreach ($results['warnings'] as $warning) {
                    $this->warn("  - {$warning}");
                }
            }
        } else {
            $this->error('✗ Calculation failed!');
        }

        if (!empty($results['errors'])) {
            $this->error('Errors:');
            foreach ($results['errors'] as $error) {
                $this->error("  - {$error}");
            }
        }

        $this->info('');
        $this->info('========================================');
        $this->info('');

        return $results['success'] ? 0 : 1;
    }
}

