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
            // ===== DRIVER UNTUK ALOKASI BIAYA GEDUNG & INFRASTRUKTUR =====
            [
                'name' => 'Luas Lantai',
                'unit_measurement' => 'm²',
                'description' => 'Luas lantai dalam meter persegi untuk alokasi biaya gedung, depresiasi, listrik, AC, kebersihan, keamanan, dan maintenance',
                'is_static' => true,
            ],
            [
                'name' => 'Konsumsi Listrik',
                'unit_measurement' => 'kWh',
                'description' => 'Konsumsi listrik aktual dalam kWh untuk alokasi biaya listrik secara lebih akurat daripada luas lantai',
            ],
            [
                'name' => 'Konsumsi Air',
                'unit_measurement' => 'm³',
                'description' => 'Konsumsi air aktual dalam meter kubik untuk alokasi biaya air secara lebih akurat daripada luas lantai',
            ],
            
            // ===== DRIVER UNTUK ALOKASI BIAYA SDM & ADMINISTRASI =====
            [
                'name' => 'Jumlah Karyawan (FTE)',
                'unit_measurement' => 'orang',
                'description' => 'Full Time Equivalent - jumlah karyawan untuk alokasi biaya SDM, kantin, pelatihan, dan administrasi umum',
            ],
            [
                'name' => 'Jumlah Pasien Rawat Inap',
                'unit_measurement' => 'pasien',
                'description' => 'Jumlah pasien rawat inap untuk alokasi biaya medical record, administrasi rawat inap, dan perawatan',
            ],
            [
                'name' => 'Jumlah Pasien Rawat Jalan',
                'unit_measurement' => 'pasien',
                'description' => 'Jumlah pasien rawat jalan untuk alokasi biaya administrasi rawat jalan dan pendaftaran',
            ],
            [
                'name' => 'Jumlah Kunjungan',
                'unit_measurement' => 'kunjungan',
                'description' => 'Jumlah kunjungan pasien (termasuk kontrol) untuk alokasi biaya administrasi, pendaftaran, dan kasir',
            ],
            
            // ===== DRIVER UNTUK ALOKASI BIAYA RAWAT INAP =====
            [
                'name' => 'Jumlah Tempat Tidur',
                'unit_measurement' => 'TT',
                'description' => 'Jumlah tempat tidur (bed capacity) untuk alokasi biaya perawatan, housekeeping, dan maintenance ruangan',
                'is_static' => true,
            ],
            [
                'name' => 'Jumlah Kamar',
                'unit_measurement' => 'kamar',
                'description' => 'Jumlah kamar untuk alokasi biaya housekeeping, maintenance, dan perawatan ruangan rawat inap',
                'is_static' => true,
            ],
            [
                'name' => 'Bed Days',
                'unit_measurement' => 'hari',
                'description' => 'Total hari rawat inap (pasien x hari rawat) untuk alokasi biaya perawatan, laundry, makanan, dan BHP yang lebih akurat',
            ],
            
            // ===== DRIVER UNTUK ALOKASI BIAYA LAUNDRY =====
            [
                'name' => 'Volume Laundry',
                'unit_measurement' => 'kg',
                'description' => 'Berat laundry aktual dalam kilogram untuk alokasi biaya laundry secara proporsional',
            ],
            
            // ===== DRIVER UNTUK ALOKASI BIAYA OPERASIONAL MEDIS =====
            [
                'name' => 'Jumlah Tindakan',
                'unit_measurement' => 'tindakan',
                'description' => 'Jumlah tindakan medis untuk alokasi biaya operasional, BHP medis, dan peralatan medis',
            ],
            [
                'name' => 'Jumlah Pemeriksaan',
                'unit_measurement' => 'pemeriksaan',
                'description' => 'Jumlah pemeriksaan untuk alokasi biaya laboratorium, radiologi, dan unit diagnostik lainnya',
            ],
            [
                'name' => 'Jumlah Sample',
                'unit_measurement' => 'sample',
                'description' => 'Jumlah sample untuk alokasi biaya laboratorium (bahan lab, reagen) secara lebih spesifik',
            ],
            [
                'name' => 'Jam Operasi',
                'unit_measurement' => 'jam',
                'description' => 'Total jam operasi bedah untuk alokasi biaya OK/Bedah, anestesi, dan peralatan operasi',
            ],
            [
                'name' => 'Jam Layanan',
                'unit_measurement' => 'jam',
                'description' => 'Total jam operasional unit layanan untuk alokasi biaya operasional umum dan overhead',
            ],
            
            // ===== DRIVER UNTUK ALOKASI BIAYA DEPRESIASI & PERALATAN =====
            [
                'name' => 'Jam Pakai Alat',
                'unit_measurement' => 'jam',
                'description' => 'Total jam penggunaan peralatan medis untuk alokasi biaya depresiasi peralatan medis secara proporsional',
            ],
            [
                'name' => 'Jumlah Unit Alat',
                'unit_measurement' => 'unit',
                'description' => 'Jumlah unit peralatan medis untuk alokasi biaya depresiasi dan maintenance peralatan',
                'is_static' => true,
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
                    'is_static' => $driver['is_static'] ?? false,
                ]
            );
        }

        $total = AllocationDriver::where('hospital_id', $hospital->id)->count();
        $this->command->info("✓ Allocation Drivers created: {$total} records");
        $this->command->info("  - Driver untuk Gedung & Infrastruktur: 3");
        $this->command->info("  - Driver untuk SDM & Administrasi: 4");
        $this->command->info("  - Driver untuk Rawat Inap: 3");
        $this->command->info("  - Driver untuk Laundry: 1");
        $this->command->info("  - Driver untuk Operasional Medis: 5");
        $this->command->info("  - Driver untuk Depresiasi & Peralatan: 2");
    }
}


