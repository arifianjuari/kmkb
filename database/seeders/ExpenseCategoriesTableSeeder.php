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
            // Gaji & Tunjangan
            ['code' => '5101', 'name' => 'Gaji Karyawan Tetap', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            ['code' => '5102', 'name' => 'Gaji Dokter Spesialis', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '5103', 'name' => 'Tunjangan Kesehatan', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            ['code' => '5104', 'name' => 'Tunjangan Transport', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            ['code' => '5105', 'name' => 'Tunjangan Makan', 'cost_type' => 'semi_variable', 'allocation_category' => 'gaji'],
            ['code' => '5106', 'name' => 'Lembur & Shift', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '5107', 'name' => 'Bonus & Insentif', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            
            // BHP Medis
            ['code' => '5201', 'name' => 'Obat-obatan', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5202', 'name' => 'Alat Kesehatan Habis Pakai', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5203', 'name' => 'Bahan Laboratorium', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5204', 'name' => 'Bahan Radiologi', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5205', 'name' => 'Bahan Operasi', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5206', 'name' => 'Gas Medis', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '5207', 'name' => 'Reagen Laboratorium', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            
            // BHP Non Medis
            ['code' => '5301', 'name' => 'Bahan Pembersih', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '5302', 'name' => 'Bahan Kantor', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '5303', 'name' => 'Bahan Makanan', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '5304', 'name' => 'Bahan Laundry', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '5305', 'name' => 'Bahan Maintenance', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            
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

