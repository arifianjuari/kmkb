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
        $drivers     = AllocationDriver::where('hospital_id', $hospital->id)->get();

        if ($costCenters->isEmpty()) {
            $this->command->info('Cost centers belum ada. Menjalankan CostCentersTableSeeder...');
            $this->call(CostCentersTableSeeder::class);
            $costCenters = CostCenter::where('hospital_id', $hospital->id)->get();
        }

        if ($drivers->isEmpty()) {
            $this->command->info('Allocation drivers belum ada. Menjalankan AllocationDriversTableSeeder...');
            $this->call(AllocationDriversTableSeeder::class);
            $drivers = AllocationDriver::where('hospital_id', $hospital->id)->get();
        }

        // Ambil support centers (yang akan dialokasikan)
        // Hanya top-level centers yang menjadi source allocation (sub-centers hanya sebagai target)
        $supportCenters = $costCenters->where('type', 'support')->whereNull('parent_id');

        // Ambil semua driver yang akan digunakan
        $luasLantaiDriver = $drivers->firstWhere('name', 'Luas Lantai');
        $karyawanDriver   = $drivers->firstWhere('name', 'Jumlah Karyawan (FTE)');
        $laundryDriver    = $drivers->firstWhere('name', 'Volume Laundry');
        $listrikDriver    = $drivers->firstWhere('name', 'Konsumsi Listrik');
        $airDriver        = $drivers->firstWhere('name', 'Konsumsi Air');

        $stepSequence    = 1;
        $created         = 0;
        $mappedSourceIds = [];

        // Helper kecil untuk menghindari duplikasi kode
        $createMap = function (CostCenter $source, AllocationDriver $driver, int $step) use ($hospital, &$created, &$mappedSourceIds) {
            AllocationMap::updateOrCreate(
                [
                    'hospital_id'            => $hospital->id,
                    'source_cost_center_id'  => $source->id,
                    'allocation_driver_id'   => $driver->id,
                ],
                [
                    'step_sequence'          => $step,
                ]
            );

            $created++;
            $mappedSourceIds[] = $source->id;
        };

        // ===== STEP 1: ALOKASI INFRASTRUKTUR DASAR (Bagian Umum, MES) =====
        // Menggunakan Luas Lantai untuk gedung, depresiasi gedung, dan maintenance umum
        if ($luasLantaiDriver) {
            $infrastructureSources = $supportCenters->filter(function ($cc) {
                return in_array($cc->code, ['UMUM', 'MES']);
            });

            if ($infrastructureSources->isNotEmpty()) {
                foreach ($infrastructureSources as $source) {
                    $createMap($source, $luasLantaiDriver, $stepSequence);
                }
                $stepSequence++;
            }
        }

        // ===== STEP 2: ALOKASI LISTRIK (Konsumsi Listrik kWh) =====
        if ($listrikDriver) {
            $listrikCenter = $supportCenters->firstWhere('code', 'LISTRIK');
            if ($listrikCenter) {
                $createMap($listrikCenter, $listrikDriver, $stepSequence);
                $stepSequence++;
            }
        }

        // ===== STEP 3: ALOKASI AIR (Konsumsi Air m3) =====
        if ($airDriver) {
            $airCenter = $supportCenters->firstWhere('code', 'AIR');
            if ($airCenter) {
                $createMap($airCenter, $airDriver, $stepSequence);
                $stepSequence++;
            }
        }

        // ===== STEP 4: ALOKASI AC & VENTILASI (Luas Lantai) =====
        if ($luasLantaiDriver) {
            $acCenter = $supportCenters->firstWhere('code', 'AC');
            if ($acCenter) {
                $createMap($acCenter, $luasLantaiDriver, $stepSequence);
                $stepSequence++;
            }
        }

        // ===== STEP 5: ALOKASI SDM & KANTIN (Jumlah Karyawan / FTE) =====
        if ($karyawanDriver) {
            $sdmSources = $supportCenters->filter(function ($cc) {
                return in_array($cc->code, ['SDM', 'KANTIN']);
            });

            if ($sdmSources->isNotEmpty()) {
                foreach ($sdmSources as $source) {
                    $createMap($source, $karyawanDriver, $stepSequence);
                }
                $stepSequence++;
            }
        }

        // ===== STEP 6: ALOKASI ADMINISTRASI (Jumlah Karyawan / FTE) =====
        // Administrasi melayani seluruh unit & pegawai, bukan hanya pasien → driver FTE lebih adil
        if ($karyawanDriver) {
            $admCenter = $supportCenters->firstWhere('code', 'ADM');
            if ($admCenter) {
                $createMap($admCenter, $karyawanDriver, $stepSequence);
                $stepSequence++;
            }
        }

        // ===== STEP 7: ALOKASI LAUNDRY (Volume Laundry) =====
        if ($laundryDriver) {
            $laundryCenter = $supportCenters->firstWhere('code', 'LAUNDRY');
            if ($laundryCenter) {
                $createMap($laundryCenter, $laundryDriver, $stepSequence);
                $stepSequence++;
            }
        }

        // ===== STEP 8: ALOKASI KEBERSIHAN / HOUSEKEEPING (Luas Lantai) =====
        if ($luasLantaiDriver) {
            $kebersihanCenter = $supportCenters->firstWhere('code', 'KEBERSIHAN');
            if ($kebersihanCenter) {
                $createMap($kebersihanCenter, $luasLantaiDriver, $stepSequence);
                $stepSequence++;
            }
        }

        // ===== STEP 9: ALOKASI KEAMANAN / SATPAM (Luas Lantai) =====
        if ($luasLantaiDriver) {
            $keamananCenter = $supportCenters->firstWhere('code', 'KEAMANAN');
            if ($keamananCenter) {
                $createMap($keamananCenter, $luasLantaiDriver, $stepSequence);
                $stepSequence++;
            }
        }

        // ===== STEP 10: ALOKASI KEBUN & PERTAMANAN (Luas Lantai) =====
        if ($luasLantaiDriver) {
            $gardenerCenter = $supportCenters->firstWhere('code', 'GARDENER');
            if ($gardenerCenter) {
                $createMap($gardenerCenter, $luasLantaiDriver, $stepSequence);
                $stepSequence++;
            }
        }

        // ===== STEP 11: ALOKASI SUPPORT CENTER LAIN YANG BELUM TERPETAKAN =====
        // Contoh: KEUANGAN, IT, LOGISTIK, GUDANG, MUTU, HUKUM, MARKETING, DIKLAT, dll.
        // Menggunakan Jumlah Karyawan (FTE) sebagai driver default.
        if ($karyawanDriver) {
            $mappedSourceIds = array_unique($mappedSourceIds);

            $otherSupportSources = $supportCenters->filter(function ($cc) use ($mappedSourceIds) {
                return !in_array($cc->id, $mappedSourceIds, true);
            });

            if ($otherSupportSources->isNotEmpty()) {
                foreach ($otherSupportSources as $source) {
                    $createMap($source, $karyawanDriver, $stepSequence);
                }
                $stepSequence++;
            }
        }

        // ===== LOGGING =====
        $mappedCount = AllocationMap::where('hospital_id', $hospital->id)->count();

        $this->command->info("✓ Allocation Maps created/updated: {$created} records");
        $this->command->info("  - Step sequence used: 1 to " . ($stepSequence - 1));
        $this->command->info("  - Support centers total : {$supportCenters->count()}");
        $this->command->info("  - Support centers mapped: {$mappedCount}");
        $this->command->info('');
        $this->command->info('Ringkasan driver alokasi:');
        $this->command->info('  - Luas Lantai        : gedung, MES, AC, kebersihan, keamanan, pertamanan');
        $this->command->info('  - Konsumsi Listrik   : instalasi listrik');
        $this->command->info('  - Konsumsi Air       : instalasi air');
        $this->command->info('  - Volume Laundry     : laundry');
        $this->command->info('  - Jumlah Karyawan    : SDM, kantin, administrasi, dan support lain yang belum terpetakan');
    }
}