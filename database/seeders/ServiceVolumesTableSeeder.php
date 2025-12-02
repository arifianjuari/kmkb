<?php

namespace Database\Seeders;

use App\Models\ServiceVolume;
use App\Models\Hospital;
use App\Models\CostReference;
use App\Models\TariffClass;
use Illuminate\Database\Seeder;

class ServiceVolumesTableSeeder extends Seeder
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

        $this->command->info('Membuat data Service Volumes...');

        // Pastikan ada cost references
        $costReferences = CostReference::where('hospital_id', $hospital->id)->get();
        if ($costReferences->isEmpty()) {
            $this->command->info('Membuat cost references...');
            $this->call(CostReferencesTableSeeder::class);
            $costReferences = CostReference::where('hospital_id', $hospital->id)->get();
        }

        // Ambil tariff classes (opsional)
        $tariffClasses = TariffClass::where('hospital_id', $hospital->id)->get();

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
            foreach ($costReferences as $costRef) {
                // Generate volume berdasarkan jenis service
                $baseVolume = $this->getBaseVolume($costRef);
                $volume = $baseVolume * (0.8 + (rand(0, 40) / 100)); // Variasi ±20%
                
                // Untuk beberapa cost references, buat per tariff class
                if ($tariffClasses->isNotEmpty() && $this->needsTariffClass($costRef)) {
                    foreach ($tariffClasses->take(3) as $tariffClass) {
                        ServiceVolume::updateOrCreate(
                            [
                                'hospital_id' => $hospital->id,
                                'period_month' => $period['month'],
                                'period_year' => $period['year'],
                                'cost_reference_id' => $costRef->id,
                                'tariff_class_id' => $tariffClass->id,
                            ],
                            [
                                'total_quantity' => round($volume * (0.3 + (rand(0, 40) / 100)), 2), // Distribusi per kelas
                            ]
                        );
                        $created++;
                    }
                } else {
                    // Tanpa tariff class
                    ServiceVolume::updateOrCreate(
                        [
                            'hospital_id' => $hospital->id,
                            'period_month' => $period['month'],
                            'period_year' => $period['year'],
                            'cost_reference_id' => $costRef->id,
                            'tariff_class_id' => null,
                        ],
                        [
                            'total_quantity' => round($volume, 2),
                        ]
                    );
                    $created++;
                }
            }
        }

        $this->command->info("✓ Service Volumes created: {$created} records for " . count($periods) . " period(s)");
    }

    /**
     * Get base volume berdasarkan jenis service
     */
    private function getBaseVolume($costRef): float
    {
        $code = strtoupper($costRef->service_code ?? '');
        $desc = strtoupper($costRef->service_description ?? '');
        
        // Laboratorium
        if (str_contains($code, 'LAB') || str_contains($desc, 'LABORATORIUM')) {
            return match(true) {
                str_contains($code, 'CBC') || str_contains($desc, 'HEMATOLOGI') => 500,
                str_contains($code, 'GLUCOSE') || str_contains($desc, 'GULA') => 800,
                str_contains($code, 'URINE') => 400,
                default => 300,
            };
        }
        
        // Radiologi
        if (str_contains($code, 'RAD') || str_contains($desc, 'RADIOLOGI')) {
            return match(true) {
                str_contains($code, 'THORAX') || str_contains($desc, 'THORAX') => 400,
                str_contains($code, 'ABDOMEN') => 200,
                str_contains($code, 'USG') || str_contains($code, 'ULTRASOUND') => 300,
                str_contains($code, 'CT') => 100,
                str_contains($code, 'MRI') => 50,
                default => 250,
            };
        }
        
        // Dokter
        if (str_contains($code, 'DRG') || str_contains($desc, 'DOKTER') || str_contains($desc, 'VISITE')) {
            return 600;
        }
        
        // Perawatan
        if (str_contains($code, 'NURSING') || str_contains($desc, 'PERAWATAN')) {
            return 2000; // Per hari untuk rawat inap
        }
        
        // Medikasi
        if (str_contains($code, 'MED') || str_contains($desc, 'OBAT')) {
            return 1000;
        }
        
        // BHP
        if (str_contains($code, 'BHP')) {
            return 2000;
        }
        
        // Administrasi
        if (str_contains($code, 'ADM') || str_contains($desc, 'ADMINISTRASI')) {
            return 1500;
        }
        
        // Kamar
        if (str_contains($code, 'ROOM') || str_contains($desc, 'KAMAR')) {
            return 4500; // Bed-days per bulan
        }
        
        return 100; // Default
    }

    /**
     * Check if cost reference needs tariff class
     */
    private function needsTariffClass($costRef): bool
    {
        $code = strtoupper($costRef->service_code ?? '');
        $desc = strtoupper($costRef->service_description ?? '');
        
        // Kamar rawat inap biasanya punya kelas tarif
        if (str_contains($code, 'ROOM') || str_contains($desc, 'KAMAR')) {
            return true;
        }
        
        // Beberapa layanan medis juga punya kelas tarif
        if (str_contains($code, 'DRG') || str_contains($desc, 'DOKTER')) {
            return true;
        }
        
        return false;
    }
}







