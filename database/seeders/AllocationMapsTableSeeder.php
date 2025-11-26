<?php

namespace Database\Seeders;

use App\Models\AllocationMap;
use App\Models\Hospital;
use App\Models\CostCenter;
use App\Models\AllocationDriver;
use Illuminate\Database\Seeder;

class AllocationMapsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospital = Hospital::first();
        if (!$hospital) {
            $this->command->error('Hospital tidak ditemukan. Jalankan HospitalsTableSeeder terlebih dahulu.');
            return;
        }

        $this->command->info('Membuat data Allocation Maps...');

        // Pastikan ada cost centers dan allocation drivers
        $costCenters = CostCenter::where('hospital_id', $hospital->id)->get();
        $drivers = AllocationDriver::where('hospital_id', $hospital->id)->get();

        if ($costCenters->isEmpty()) {
            $this->command->info('Membuat cost centers...');
            $this->call(CostCentersTableSeeder::class);
            $costCenters = CostCenter::where('hospital_id', $hospital->id)->get();
        }

        if ($drivers->isEmpty()) {
            $this->command->info('Membuat allocation drivers...');
            $this->call(AllocationDriversTableSeeder::class);
            $drivers = AllocationDriver::where('hospital_id', $hospital->id)->get();
        }

        // Ambil support centers (yang akan dialokasikan)
        $supportCenters = $costCenters->where('type', 'support')->whereNull('parent_id');
        
        // Ambil driver yang sesuai
        $luasLantaiDriver = $drivers->firstWhere('name', 'Luas Lantai');
        $karyawanDriver = $drivers->firstWhere('name', 'Jumlah Karyawan (FTE)');
        $laundryDriver = $drivers->firstWhere('name', 'Volume Laundry');
        $listrikDriver = $drivers->firstWhere('name', 'Konsumsi Listrik');
        $airDriver = $drivers->firstWhere('name', 'Konsumsi Air');

        $stepSequence = 1;
        $created = 0;

        // Step 1: Alokasi berdasarkan Luas Lantai (untuk biaya gedung, listrik, AC)
        if ($luasLantaiDriver) {
            $sources = $supportCenters->filter(function($cc) {
                return in_array($cc->code, ['MES', 'UMUM', 'LISTRIK', 'AC']);
            });
            
            foreach ($sources as $source) {
                AllocationMap::updateOrCreate(
                    [
                        'hospital_id' => $hospital->id,
                        'source_cost_center_id' => $source->id,
                        'allocation_driver_id' => $luasLantaiDriver->id,
                    ],
                    [
                        'step_sequence' => $stepSequence,
                    ]
                );
                $created++;
            }
            $stepSequence++;
        }

        // Step 2: Alokasi berdasarkan Jumlah Karyawan (untuk SDM, kantin)
        if ($karyawanDriver) {
            $sources = $supportCenters->filter(function($cc) {
                return in_array($cc->code, ['SDM', 'KANTIN']);
            });
            
            foreach ($sources as $source) {
                AllocationMap::updateOrCreate(
                    [
                        'hospital_id' => $hospital->id,
                        'source_cost_center_id' => $source->id,
                        'allocation_driver_id' => $karyawanDriver->id,
                    ],
                    [
                        'step_sequence' => $stepSequence,
                    ]
                );
                $created++;
            }
            $stepSequence++;
        }

        // Step 3: Alokasi Laundry berdasarkan Volume Laundry
        if ($laundryDriver) {
            $laundryCenter = $supportCenters->firstWhere('code', 'LAUNDRY');
            if ($laundryCenter) {
                AllocationMap::updateOrCreate(
                    [
                        'hospital_id' => $hospital->id,
                        'source_cost_center_id' => $laundryCenter->id,
                        'allocation_driver_id' => $laundryDriver->id,
                    ],
                    [
                        'step_sequence' => $stepSequence,
                    ]
                );
                $created++;
            }
            $stepSequence++;
        }

        // Step 4: Alokasi Listrik berdasarkan Konsumsi Listrik
        if ($listrikDriver) {
            $listrikCenter = $supportCenters->firstWhere('code', 'LISTRIK');
            if ($listrikCenter) {
                AllocationMap::updateOrCreate(
                    [
                        'hospital_id' => $hospital->id,
                        'source_cost_center_id' => $listrikCenter->id,
                        'allocation_driver_id' => $listrikDriver->id,
                    ],
                    [
                        'step_sequence' => $stepSequence,
                    ]
                );
                $created++;
            }
            $stepSequence++;
        }

        // Step 5: Alokasi Air berdasarkan Konsumsi Air
        if ($airDriver) {
            $airCenter = $supportCenters->firstWhere('code', 'AIR');
            if ($airCenter) {
                AllocationMap::updateOrCreate(
                    [
                        'hospital_id' => $hospital->id,
                        'source_cost_center_id' => $airCenter->id,
                        'allocation_driver_id' => $airDriver->id,
                    ],
                    [
                        'step_sequence' => $stepSequence,
                    ]
                );
                $created++;
            }
            $stepSequence++;
        }

        // Step 6: Alokasi lainnya (Kebersihan, Keamanan, dll) berdasarkan Luas Lantai
        if ($luasLantaiDriver) {
            $otherSources = $supportCenters->filter(function($cc) {
                return in_array($cc->code, ['KEBERSIHAN', 'KEAMANAN', 'GARDENER', 'ADM']);
            });
            
            foreach ($otherSources as $source) {
                AllocationMap::updateOrCreate(
                    [
                        'hospital_id' => $hospital->id,
                        'source_cost_center_id' => $source->id,
                        'allocation_driver_id' => $luasLantaiDriver->id,
                    ],
                    [
                        'step_sequence' => $stepSequence,
                    ]
                );
                $created++;
            }
        }

        $this->command->info("✓ Allocation Maps created: {$created} records");
        $this->command->info("  - Step sequence: 1 to " . ($stepSequence - 1));
    }
}

