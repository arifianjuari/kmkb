<?php

namespace Database\Seeders;

use App\Models\CostCenter;
use App\Models\Hospital;
use Illuminate\Database\Seeder;

class CostCentersTableSeeder extends Seeder
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

        $this->command->info('Membuat data Cost Centers...');

        // Definisikan struktur cost centers dengan hierarki
        $costCentersData = [
            // ===== REVENUE-GENERATING CENTERS =====
            [
                'code' => 'IGD',
                'name' => 'Instalasi Gawat Darurat',
                'type' => 'revenue',
                'parent_id' => null,
            ],
            [
                'code' => 'RAWAT-INAP',
                'name' => 'Rawat Inap',
                'type' => 'revenue',
                'parent_id' => null,
            ],
            [
                'code' => 'RAWAT-JALAN',
                'name' => 'Rawat Jalan',
                'type' => 'revenue',
                'parent_id' => null,
            ],
            [
                'code' => 'LAB',
                'name' => 'Laboratorium',
                'type' => 'revenue',
                'parent_id' => null,
            ],
            [
                'code' => 'RAD',
                'name' => 'Radiologi',
                'type' => 'revenue',
                'parent_id' => null,
            ],
            [
                'code' => 'OK',
                'name' => 'Instalasi Bedah Sentral (IBS)',
                'type' => 'revenue',
                'parent_id' => null,
            ],
            [
                'code' => 'ICU',
                'name' => 'Intensive Care Unit',
                'type' => 'revenue',
                'parent_id' => null,
            ],
            [
                'code' => 'HCU',
                'name' => 'High Care Unit',
                'type' => 'revenue',
                'parent_id' => null,
            ],
            [
                'code' => 'PICU',
                'name' => 'Pediatric Intensive Care Unit',
                'type' => 'revenue',
                'parent_id' => null,
            ],
            [
                'code' => 'NICU',
                'name' => 'Neonatal Intensive Care Unit',
                'type' => 'revenue',
                'parent_id' => null,
            ],
            [
                'code' => 'FARMASI',
                'name' => 'Instalasi Farmasi',
                'type' => 'revenue',
                'parent_id' => null,
            ],
            [
                'code' => 'REHAB',
                'name' => 'Instalasi Rehabilitasi Medik',
                'type' => 'revenue',
                'parent_id' => null,
            ],
            [
                'code' => 'HEMODIALISA',
                'name' => 'Unit Hemodialisa',
                'type' => 'revenue',
                'parent_id' => null,
            ],
            
            // ===== SUPPORT CENTERS =====
            [
                'code' => 'ADM',
                'name' => 'Administrasi',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'SDM',
                'name' => 'Sumber Daya Manusia',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'KEUANGAN',
                'name' => 'Keuangan & Akuntansi',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'IT',
                'name' => 'Teknologi Informasi',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'UMUM',
                'name' => 'Bagian Umum',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'LOGISTIK',
                'name' => 'Logistik & Pengadaan',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'GUDANG',
                'name' => 'Gudang',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'LAUNDRY',
                'name' => 'Laundry',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'KANTIN',
                'name' => 'Kantin',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'KEBERSIHAN',
                'name' => 'Kebersihan & Housekeeping',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'KEAMANAN',
                'name' => 'Keamanan & Satpam',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'GARDENER',
                'name' => 'Kebun & Pertamanan',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'MES',
                'name' => 'Maintenance & Engineering Services',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'LISTRIK',
                'name' => 'Instalasi Listrik',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'AIR',
                'name' => 'Instalasi Air',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'AC',
                'name' => 'Instalasi AC & Ventilasi',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'MUTU',
                'name' => 'Penjaminan Mutu',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'HUKUM',
                'name' => 'Hukum & Compliance',
                'type' => 'support',
                'parent_id' => null,
            ],
            [
                'code' => 'MARKETING',
                'name' => 'Marketing & Komunikasi',
                'type' => 'support',
                'parent_id' => null,
            ],
        ];

        // Buat cost centers utama (tanpa parent)
        $createdCenters = [];
        foreach ($costCentersData as $data) {
            $center = CostCenter::updateOrCreate(
                [
                    'code' => $data['code'],
                    'hospital_id' => $hospital->id,
                ],
                [
                    'name' => $data['name'],
                    'type' => $data['type'],
                    'parent_id' => $data['parent_id'],
                    'is_active' => true,
                ]
            );
            $createdCenters[$data['code']] = $center;
        }

        // Buat sub-cost centers (child centers) untuk beberapa departemen besar
        $subCenters = [
            // Sub-centers untuk Rawat Inap
            [
                'code' => 'RI-KLS1',
                'name' => 'Rawat Inap Kelas 1',
                'type' => 'revenue',
                'parent_code' => 'RAWAT-INAP',
            ],
            [
                'code' => 'RI-KLS2',
                'name' => 'Rawat Inap Kelas 2',
                'type' => 'revenue',
                'parent_code' => 'RAWAT-INAP',
            ],
            [
                'code' => 'RI-KLS3',
                'name' => 'Rawat Inap Kelas 3',
                'type' => 'revenue',
                'parent_code' => 'RAWAT-INAP',
            ],
            [
                'code' => 'RI-VIP',
                'name' => 'Rawat Inap VIP',
                'type' => 'revenue',
                'parent_code' => 'RAWAT-INAP',
            ],
            
            // Sub-centers untuk Rawat Jalan
            [
                'code' => 'RJ-POLI-UMUM',
                'name' => 'Poli Umum',
                'type' => 'revenue',
                'parent_code' => 'RAWAT-JALAN',
            ],
            [
                'code' => 'RJ-POLI-SPESIALIS',
                'name' => 'Poli Spesialis',
                'type' => 'revenue',
                'parent_code' => 'RAWAT-JALAN',
            ],
            
            // Sub-centers untuk Laboratorium
            [
                'code' => 'LAB-KLINIK',
                'name' => 'Lab Klinik',
                'type' => 'revenue',
                'parent_code' => 'LAB',
            ],
            [
                'code' => 'LAB-PATOLOGI',
                'name' => 'Lab Patologi Anatomi',
                'type' => 'revenue',
                'parent_code' => 'LAB',
            ],
            [
                'code' => 'LAB-MIKROBIOLOGI',
                'name' => 'Lab Mikrobiologi',
                'type' => 'revenue',
                'parent_code' => 'LAB',
            ],
            
            // Sub-centers untuk Radiologi
            [
                'code' => 'RAD-FOTO',
                'name' => 'Radiologi Foto',
                'type' => 'revenue',
                'parent_code' => 'RAD',
            ],
            [
                'code' => 'RAD-CT',
                'name' => 'Radiologi CT Scan',
                'type' => 'revenue',
                'parent_code' => 'RAD',
            ],
            [
                'code' => 'RAD-MRI',
                'name' => 'Radiologi MRI',
                'type' => 'revenue',
                'parent_code' => 'RAD',
            ],
            [
                'code' => 'RAD-USG',
                'name' => 'Radiologi USG',
                'type' => 'revenue',
                'parent_code' => 'RAD',
            ],
            
            // Sub-centers untuk Administrasi
            [
                'code' => 'ADM-PENDAFTARAN',
                'name' => 'Pendaftaran',
                'type' => 'support',
                'parent_code' => 'ADM',
            ],
            [
                'code' => 'ADM-KASIR',
                'name' => 'Kasir',
                'type' => 'support',
                'parent_code' => 'ADM',
            ],
            [
                'code' => 'ADM-MEDREK',
                'name' => 'Medical Record',
                'type' => 'support',
                'parent_code' => 'ADM',
            ],
            
            // Sub-centers untuk MES
            [
                'code' => 'MES-ELEKTRIK',
                'name' => 'Maintenance Elektrik',
                'type' => 'support',
                'parent_code' => 'MES',
            ],
            [
                'code' => 'MES-MEKANIK',
                'name' => 'Maintenance Mekanik',
                'type' => 'support',
                'parent_code' => 'MES',
            ],
            [
                'code' => 'MES-SIPIL',
                'name' => 'Maintenance Sipil',
                'type' => 'support',
                'parent_code' => 'MES',
            ],
        ];

        // Buat sub-cost centers
        foreach ($subCenters as $subData) {
            $parent = $createdCenters[$subData['parent_code']] ?? null;
            if ($parent) {
                CostCenter::updateOrCreate(
                    [
                        'code' => $subData['code'],
                        'hospital_id' => $hospital->id,
                    ],
                    [
                        'name' => $subData['name'],
                        'type' => $subData['type'],
                        'parent_id' => $parent->id,
                        'is_active' => true,
                    ]
                );
            }
        }

        $totalCenters = CostCenter::where('hospital_id', $hospital->id)->count();
        $revenueCenters = CostCenter::where('hospital_id', $hospital->id)->where('type', 'revenue')->count();
        $supportCenters = CostCenter::where('hospital_id', $hospital->id)->where('type', 'support')->count();

        $this->command->info('✓ Cost Centers created successfully!');
        $this->command->info("  - Total Cost Centers: {$totalCenters}");
        $this->command->info("  - Revenue Centers: {$revenueCenters}");
        $this->command->info("  - Support Centers: {$supportCenters}");
    }
}




