<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\Hospital;
use Illuminate\Database\Seeder;

class ExpenseCategoriesTableSeeder extends Seeder
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

        $this->command->info('Membuat data Expense Categories...');

        $expenseCategories = [
            // Gaji & Tunjangan - Umum
            ['code' => '5101', 'name' => 'Gaji Karyawan Tetap', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            ['code' => '5102', 'name' => 'Gaji Dokter Spesialis', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '5103', 'name' => 'Tunjangan Kesehatan', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            ['code' => '5104', 'name' => 'Tunjangan Transport', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            ['code' => '5105', 'name' => 'Tunjangan Makan', 'cost_type' => 'semi_variable', 'allocation_category' => 'gaji'],
            ['code' => '5106', 'name' => 'Lembur & Shift', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '5107', 'name' => 'Bonus & Insentif', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],

            // Gaji & Tunjangan per Unit Layanan
            ['code' => '5110', 'name' => 'Gaji Tenaga Medis IGD', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '5111', 'name' => 'Gaji Perawat IGD', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '5120', 'name' => 'Gaji Tenaga Medis Rawat Jalan', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '5121', 'name' => 'Gaji Perawat Rawat Jalan', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '5130', 'name' => 'Gaji Tenaga Medis Rawat Inap', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '5131', 'name' => 'Gaji Perawat Rawat Inap', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '5140', 'name' => 'Gaji Tenaga Medis OK/Bedah', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '5141', 'name' => 'Gaji Perawat OK/Bedah', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '5150', 'name' => 'Gaji Tenaga Medis ICU/ICCU', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '5151', 'name' => 'Gaji Perawat ICU/ICCU', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '5160', 'name' => 'Gaji Tenaga Medis Laboratorium', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            ['code' => '5161', 'name' => 'Gaji Tenaga Medis Radiologi', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],

            // BHP Medis - Umum
            ['code' => '5201', 'name' => 'Obat-obatan', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5202', 'name' => 'Alat Kesehatan Habis Pakai', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5203', 'name' => 'Bahan Laboratorium', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5204', 'name' => 'Bahan Radiologi', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5205', 'name' => 'Bahan Operasi', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5206', 'name' => 'Gas Medis', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5207', 'name' => 'Reagen Laboratorium', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],

            // BHP Medis per Unit Layanan
            ['code' => '5210', 'name' => 'Obat IGD', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5211', 'name' => 'Obat Rawat Jalan', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5212', 'name' => 'Obat Rawat Inap', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5220', 'name' => 'BHP Medis IGD', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5221', 'name' => 'BHP Medis Rawat Jalan', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5222', 'name' => 'BHP Medis Rawat Inap', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5230', 'name' => 'BHP Medis OK/Bedah', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5231', 'name' => 'BHP Medis ICU/ICCU', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5240', 'name' => 'Bahan Pemeriksaan Laboratorium', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5241', 'name' => 'Bahan Pemeriksaan Radiologi', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],

            // BHP Non Medis - Umum
            ['code' => '5301', 'name' => 'Bahan Pembersih', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '5302', 'name' => 'Bahan Kantor', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '5303', 'name' => 'Bahan Makanan', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '5304', 'name' => 'Bahan Laundry', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '5305', 'name' => 'Bahan Maintenance', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],

            // BHP Non Medis per Unit Pendukung
            ['code' => '5310', 'name' => 'ATK Administrasi', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '5311', 'name' => 'ATK Rawat Jalan', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '5312', 'name' => 'ATK Rawat Inap', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '5320', 'name' => 'Bahan Kebersihan Umum', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '5321', 'name' => 'Bahan Kebersihan Ruang Rawat Inap', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '5330', 'name' => 'Bahan Makanan Dapur Gizi', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '5340', 'name' => 'Bahan Laundry Linen', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],

            // Depresiasi
            ['code' => '5401', 'name' => 'Depresiasi Gedung', 'cost_type' => 'fixed', 'allocation_category' => 'depresiasi'],
            ['code' => '5402', 'name' => 'Depresiasi Peralatan Medis', 'cost_type' => 'fixed', 'allocation_category' => 'depresiasi'],
            ['code' => '5403', 'name' => 'Depresiasi Peralatan Non Medis', 'cost_type' => 'fixed', 'allocation_category' => 'depresiasi'],
            ['code' => '5404', 'name' => 'Depresiasi Kendaraan', 'cost_type' => 'fixed', 'allocation_category' => 'depresiasi'],
            ['code' => '5405', 'name' => 'Depresiasi IT & Komputer', 'cost_type' => 'fixed', 'allocation_category' => 'depresiasi'],

            // Lain-lain
            ['code' => '5501', 'name' => 'Listrik', 'cost_type' => 'semi_variable', 'allocation_category' => 'lain_lain'],
            ['code' => '5502', 'name' => 'Air', 'cost_type' => 'semi_variable', 'allocation_category' => 'lain_lain'],
            ['code' => '5503', 'name' => 'Telepon & Internet', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '5504', 'name' => 'Sewa Gedung', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '5505', 'name' => 'Asuransi', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '5506', 'name' => 'Pajak', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '5507', 'name' => 'Konsultan & Jasa Profesional', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '5508', 'name' => 'Pelatihan & Pengembangan', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '5509', 'name' => 'Marketing & Promosi', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '5510', 'name' => 'Perawatan & Maintenance', 'cost_type' => 'semi_variable', 'allocation_category' => 'lain_lain'],
            ['code' => '5511', 'name' => 'Biaya Keamanan & Satpam', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '5512', 'name' => 'Biaya Kebersihan Pihak Ketiga', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
        ];

        foreach ($expenseCategories as $category) {
            ExpenseCategory::updateOrCreate(
                [
                    'account_code' => $category['code'],
                    'hospital_id' => $hospital->id,
                ],
                [
                    'account_name' => $category['name'],
                    'cost_type' => $category['cost_type'],
                    'allocation_category' => $category['allocation_category'],
                    'is_active' => true,
                ]
            );
        }

        $total = ExpenseCategory::where('hospital_id', $hospital->id)->count();
        $this->command->info("✓ Expense Categories created: {$total} records");
    }
}

