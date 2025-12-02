<?php

namespace Database\Seeders;

use App\Models\DriverStatistic;
use App\Models\Hospital;
use App\Models\CostCenter;
use App\Models\AllocationDriver;
use Illuminate\Database\Seeder;

class DriverStatisticsTableSeeder extends Seeder
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

        $this->command->info('Membuat data Driver Statistics...');

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

        // Generate data untuk 3 bulan terakhir
        $periods = [];
        $currentMonth = (int) date('m');
        $currentYear = (int) date('Y');
        
        for ($i = 2; $i >= 0; $i--) {
            $month = $currentMonth - $i;
            $year = $currentYear;
            if ($month <= 0) {
                $month += 12;
                $year -= 1;
            }
            $periods[] = ['month' => $month, 'year' => $year];
        }

        $created = 0;
        foreach ($periods as $period) {
            foreach ($costCenters as $costCenter) {
                foreach ($drivers as $driver) {
                    // Generate value berdasarkan driver type dan cost center
                    $value = $this->getDriverValue($costCenter, $driver);
                    
                    if ($value > 0) {
                        DriverStatistic::updateOrCreate(
                            [
                                'hospital_id' => $hospital->id,
                                'period_month' => $period['month'],
                                'period_year' => $period['year'],
                                'cost_center_id' => $costCenter->id,
                                'allocation_driver_id' => $driver->id,
                            ],
                            [
                                'value' => $value,
                            ]
                        );
                        $created++;
                    }
                }
            }
        }

        $this->command->info("✓ Driver Statistics created: {$created} records for " . count($periods) . " period(s)");
    }

    /**
     * Get driver value berdasarkan cost center dan driver type
     */
    private function getDriverValue($costCenter, $driver): float
    {
        $driverName = strtolower($driver->name);
        
        // Luas Lantai (m²)
        if (str_contains($driverName, 'luas') || str_contains($driverName, 'lantai')) {
            return match($costCenter->code) {
                'IGD' => 500,
                'RAWAT-INAP' => 2000,
                'RAWAT-JALAN' => 1500,
                'LAB' => 800,
                'RAD' => 1000,
                'OK' => 1200,
                'ICU', 'HCU', 'PICU', 'NICU' => 600,
                'ADM' => 400,
                'MES' => 300,
                'LAUNDRY' => 200,
                'KEBERSIHAN' => 100,
                default => 300,
            };
        }
        
        // Jumlah Karyawan (FTE)
        if (str_contains($driverName, 'karyawan') || str_contains($driverName, 'fte')) {
            return match($costCenter->code) {
                'IGD' => 25,
                'RAWAT-INAP' => 80,
                'RAWAT-JALAN' => 40,
                'LAB' => 15,
                'RAD' => 20,
                'OK' => 30,
                'ICU' => 20,
                'ADM' => 30,
                'SDM' => 15,
                'KEUANGAN' => 10,
                'MES' => 12,
                'LAUNDRY' => 8,
                'KEBERSIHAN' => 20,
                'KEAMANAN' => 15,
                default => 10,
            };
        }
        
        // Jumlah Pasien
        if (str_contains($driverName, 'pasien')) {
            if ($costCenter->type === 'revenue') {
                return match($costCenter->code) {
                    'IGD' => 500,
                    'RAWAT-INAP' => 200,
                    'RAWAT-JALAN' => 1000,
                    'LAB' => 800,
                    'RAD' => 600,
                    default => 100,
                };
            }
            return 0;
        }
        
        // Jumlah Tempat Tidur
        if (str_contains($driverName, 'tempat tidur') || str_contains($driverName, 'tt')) {
            if (in_array($costCenter->code, ['RAWAT-INAP', 'ICU', 'HCU', 'PICU', 'NICU'])) {
                return match($costCenter->code) {
                    'RAWAT-INAP' => 150,
                    'ICU' => 10,
                    'HCU' => 8,
                    'PICU' => 6,
                    'NICU' => 8,
                    default => 0,
                };
            }
            return 0;
        }
        
        // Volume Laundry (kg)
        if (str_contains($driverName, 'laundry')) {
            if ($costCenter->code === 'LAUNDRY') {
                return 5000; // Total laundry per bulan
            }
            // Distribusi ke cost centers yang menggunakan laundry
            if (in_array($costCenter->code, ['RAWAT-INAP', 'IGD', 'OK', 'ICU'])) {
                return match($costCenter->code) {
                    'RAWAT-INAP' => 3000,
                    'IGD' => 500,
                    'OK' => 800,
                    'ICU' => 700,
                    default => 0,
                };
            }
            return 0;
        }
        
        // Jam Layanan
        if (str_contains($driverName, 'jam') && str_contains($driverName, 'layanan')) {
            if ($costCenter->type === 'revenue') {
                return 720; // 24 jam x 30 hari
            }
            return 240; // 8 jam x 30 hari untuk support
        }
        
        // Jumlah Kunjungan
        if (str_contains($driverName, 'kunjungan')) {
            if ($costCenter->code === 'RAWAT-JALAN' || $costCenter->code === 'IGD') {
                return match($costCenter->code) {
                    'RAWAT-JALAN' => 1000,
                    'IGD' => 500,
                    default => 0,
                };
            }
            return 0;
        }
        
        // Jumlah Tindakan
        if (str_contains($driverName, 'tindakan')) {
            if ($costCenter->type === 'revenue') {
                return match($costCenter->code) {
                    'LAB' => 800,
                    'RAD' => 600,
                    'OK' => 100,
                    default => 200,
                };
            }
            return 0;
        }
        
        // Konsumsi Listrik (kWh)
        if (str_contains($driverName, 'listrik') || str_contains($driverName, 'kwh')) {
            return match($costCenter->code) {
                'IGD' => 5000,
                'RAWAT-INAP' => 20000,
                'RAWAT-JALAN' => 8000,
                'LAB' => 4000,
                'RAD' => 6000,
                'OK' => 5000,
                'ICU' => 4000,
                'MES' => 3000,
                default => 2000,
            };
        }
        
        // Konsumsi Air (m³)
        if (str_contains($driverName, 'air') || str_contains($driverName, 'm³')) {
            return match($costCenter->code) {
                'RAWAT-INAP' => 500,
                'IGD' => 100,
                'OK' => 80,
                'LAUNDRY' => 200,
                'KEBERSIHAN' => 150,
                default => 50,
            };
        }
        
        return 0;
    }
}







