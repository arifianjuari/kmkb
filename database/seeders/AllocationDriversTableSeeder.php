<?php

namespace Database\Seeders;

use App\Models\AllocationDriver;
use App\Models\Hospital;
use Illuminate\Database\Seeder;

class AllocationDriversTableSeeder extends Seeder
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

        $this->command->info('Membuat data Allocation Drivers...');

        $drivers = [
            [
                'name' => 'Luas Lantai',
                'unit_measurement' => 'm²',
                'description' => 'Luas lantai dalam meter persegi untuk alokasi biaya gedung, listrik, AC',
            ],
            [
                'name' => 'Jumlah Karyawan (FTE)',
                'unit_measurement' => 'orang',
                'description' => 'Full Time Equivalent - jumlah karyawan untuk alokasi biaya SDM, kantin',
            ],
            [
                'name' => 'Jumlah Pasien',
                'unit_measurement' => 'pasien',
                'description' => 'Jumlah pasien untuk alokasi biaya administrasi, medical record',
            ],
            [
                'name' => 'Jumlah Tempat Tidur',
                'unit_measurement' => 'TT',
                'description' => 'Jumlah tempat tidur untuk alokasi biaya perawatan, laundry',
            ],
            [
                'name' => 'Volume Laundry',
                'unit_measurement' => 'kg',
                'description' => 'Berat laundry dalam kilogram untuk alokasi biaya laundry',
            ],
            [
                'name' => 'Jam Layanan',
                'unit_measurement' => 'jam',
                'description' => 'Total jam operasional untuk alokasi biaya operasional',
            ],
            [
                'name' => 'Jumlah Kunjungan',
                'unit_measurement' => 'kunjungan',
                'description' => 'Jumlah kunjungan pasien untuk alokasi biaya administrasi',
            ],
            [
                'name' => 'Jumlah Tindakan',
                'unit_measurement' => 'tindakan',
                'description' => 'Jumlah tindakan medis untuk alokasi biaya operasional',
            ],
            [
                'name' => 'Konsumsi Listrik',
                'unit_measurement' => 'kWh',
                'description' => 'Konsumsi listrik dalam kWh untuk alokasi biaya listrik',
            ],
            [
                'name' => 'Konsumsi Air',
                'unit_measurement' => 'm³',
                'description' => 'Konsumsi air dalam meter kubik untuk alokasi biaya air',
            ],
        ];

        foreach ($drivers as $driver) {
            AllocationDriver::updateOrCreate(
                [
                    'name' => $driver['name'],
                    'hospital_id' => $hospital->id,
                ],
                [
                    'unit_measurement' => $driver['unit_measurement'],
                    'description' => $driver['description'],
                ]
            );
        }

        $total = AllocationDriver::where('hospital_id', $hospital->id)->count();
        $this->command->info("✓ Allocation Drivers created: {$total} records");
    }
}

