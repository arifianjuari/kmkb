<?php

namespace App\Console\Commands;

use App\Models\AllocationDriver;
use App\Models\CostReference;
use App\Models\UnitOfMeasurement;
use App\Models\StandardResourceUsage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateUomData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uom:migrate-existing-data {--hospital= : ID of the hospital (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing free-text units to UoM master table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $hospitalId = $this->option('hospital');

        if ($hospitalId) {
            $this->info("Migrating data for hospital ID: {$hospitalId}");
            $this->migrateForHospital($hospitalId);
        } else {
            // Get all hospitals that have allocation drivers or cost references
            $hospitalIds = AllocationDriver::select('hospital_id')->distinct()->pluck('hospital_id')
                ->merge(CostReference::select('hospital_id')->distinct()->pluck('hospital_id'))
                ->merge(StandardResourceUsage::select('hospital_id')->distinct()->pluck('hospital_id'))
                ->unique();

            foreach ($hospitalIds as $id) {
                if (!$id) continue;
                $this->info("Migrating data for hospital ID: {$id}");
                $this->migrateForHospital($id);
            }
        }

        $this->info('UoM migration completed successfully.');
        return 0;
    }

    private function migrateForHospital($hospitalId)
    {
        DB::transaction(function () use ($hospitalId) {
            // 1. Seed initial standard UoMs if empty
            if (UnitOfMeasurement::where('hospital_id', $hospitalId)->count() == 0) {
                $this->call('db:seed', [
                    '--class' => 'UnitOfMeasurementSeeder',
                    '--force' => true,
                ]);
            }

            // 2. Migrate Allocation Drivers
            $this->info("Processing Allocation Drivers...");
            $drivers = AllocationDriver::where('hospital_id', $hospitalId)
                ->whereNull('unit_of_measurement_id')
                ->whereNotNull('unit_measurement')
                ->get();

            $driverCount = 0;
            foreach ($drivers as $driver) {
                $unitText = trim($driver->unit_measurement);
                if (empty($unitText)) continue;

                $uom = $this->findOrCreateUom($hospitalId, $unitText, 'allocation');
                
                $driver->update(['unit_of_measurement_id' => $uom->id]);
                $driverCount++;
            }
            $this->info("Updated {$driverCount} allocation drivers.");

            // 3. Migrate Cost References
            $this->info("Processing Cost References...");
            $costRefs = CostReference::where('hospital_id', $hospitalId)
                ->whereNull('unit_of_measurement_id')
                ->whereNotNull('unit')
                ->get();
            
            $refCount = 0;
            foreach ($costRefs as $ref) {
                $unitText = trim($ref->unit);
                if (empty($unitText)) continue;

                $uom = $this->findOrCreateUom($hospitalId, $unitText, 'service');
                
                $ref->update(['unit_of_measurement_id' => $uom->id]);
                $refCount++;
            }
            $this->info("Updated {$refCount} cost references.");

            // 4. Migrate Standard Resource Usage
            $this->info("Processing Standard Resource Usage...");
            $srus = StandardResourceUsage::where('hospital_id', $hospitalId)
                ->whereNull('unit_of_measurement_id')
                ->whereNotNull('unit')
                ->get();
            
            $sruCount = 0;
            foreach ($srus as $sru) {
                $unitText = trim($sru->unit);
                if (empty($unitText)) continue;

                $uom = $this->findOrCreateUom($hospitalId, $unitText, 'service'); // Treat BOM units as service/production related
                
                $sru->update(['unit_of_measurement_id' => $uom->id]);
                $sruCount++;
            }
            $this->info("Updated {$sruCount} standard resource usages.");
        });
    }

    private function findOrCreateUom($hospitalId, $unitText, $contextPreference)
    {
        $normalizedUnit = strtolower($unitText);

        // match by code, name, or symbol
        $uom = UnitOfMeasurement::where('hospital_id', $hospitalId)
            ->where(function($q) use ($normalizedUnit, $unitText) {
                $q->whereRaw('LOWER(code) = ?', [$normalizedUnit])
                  ->orWhereRaw('LOWER(name) = ?', [$normalizedUnit])
                  ->orWhere('symbol', $unitText); // Case sensitive match for symbol often better (e.g. 'm' vs 'M')
            })
            ->first();

        if ($uom) {
            // Update context if needed (e.g. found 'count' which was 'service' but now used in 'allocation')
            if ($uom->context !== 'both' && $uom->context !== $contextPreference) {
                 $uom->update(['context' => 'both']);
            }
            return $uom;
        }

        // Create new if not found
        $code = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $unitText));
        if (empty($code)) {
            $code = 'uom_' . substr(md5($unitText), 0, 8);
        }

        // Avoid duplicate codes if similar names resolve to same code
        $counter = 1;
        $originalCode = $code;
        while (UnitOfMeasurement::where('hospital_id', $hospitalId)->where('code', $code)->exists()) {
            $code = $originalCode . $counter++;
        }

        $this->info("Creating new UoM: {$unitText} (Code: {$code})");

        return UnitOfMeasurement::create([
            'hospital_id' => $hospitalId,
            'code' => $code,
            'name' => ucwords($unitText),
            'symbol' => strlen($unitText) <= 20 ? $unitText : null,
            'category' => 'other', // Default to other since we don't know
            'context' => $contextPreference,
            'is_active' => true,
        ]);
    }
}
