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

        $this->command->info('Membuat data Expense Categories berdasarkan COA...');

        $expenseCategories = [
            // ============================================
            // 5.1 HPP LAYANAN MEDIS (511)
            // ============================================
            
            // HPP Rawat Jalan (511 11)
            ['code' => '51111001', 'name' => 'Jasa Dokter Poli Spesialis Utama', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51111002', 'name' => 'Jasa Dokter Poli Spesialis Umum & BPJS', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51111003', 'name' => 'Jasa Perawat Poli Spesialis', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            
            // HPP Rawat Darurat (511 12)
            ['code' => '51112003', 'name' => 'Jasa Dokter Instalasi Rawat Darurat dan Trauma Center', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112004', 'name' => 'Jasa Perawat Rawat Darurat', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            
            // Jasa Visitasi Rawat Inap (511 12)
            ['code' => '51112001', 'name' => 'Visitasi Rawat Inap VVIP', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112002', 'name' => 'Visitasi Rawat Inap VIP', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112005', 'name' => 'Visitasi Rawat Inap Utama', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112006', 'name' => 'Visitasi Rawat Inap Kelas I', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112007', 'name' => 'Visitasi Rawat Inap Kelas II', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112008', 'name' => 'Visitasi Rawat Inap Kelas III', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112009', 'name' => 'Visitasi Rawat Inap Kelas I BPJS', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112010', 'name' => 'Visitasi Rawat Inap Kelas II BPJS', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112011', 'name' => 'Visitasi Rawat Inap Kelas III BPJS', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112012', 'name' => 'Visitasi Rawat Inap ICU Utama', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112013', 'name' => 'Visitasi Rawat Inap ICU Umum', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112014', 'name' => 'Visitasi Rawat Inap ICU BPJS', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            
            // Jasa Perawat Rawat Inap (511 12 021-032)
            ['code' => '51112021', 'name' => 'Jasa Perawat Ruang Rawat Inap VVIP', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112022', 'name' => 'Jasa Perawat Ruang Rawat Inap VIP', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112023', 'name' => 'Jasa Perawat Ruang Rawat Inap Utama', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112024', 'name' => 'Jasa Perawat Ruang Rawat Inap Kelas I', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112025', 'name' => 'Jasa Perawat Ruang Rawat Inap Kelas II', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112026', 'name' => 'Jasa Perawat Ruang Rawat Inap Kelas III', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112027', 'name' => 'Jasa Perawat Ruang Rawat Inap Kelas I BPJS', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112028', 'name' => 'Jasa Perawat Ruang Rawat Inap Kelas II BPJS', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112029', 'name' => 'Jasa Perawat Ruang Rawat Inap Kelas III BPJS', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112030', 'name' => 'Jasa Perawat Ruang Rawat Inap ICU Utama', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112031', 'name' => 'Jasa Perawat Ruang Rawat Inap ICU Umum', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51112032', 'name' => 'Jasa Perawat Ruang Rawat Inap ICU BPJS', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            
            // ============================================
            // 5.2 HPP PENUNJANG MEDIS (512)
            // ============================================
            
            // HPP Obat, Alkes dan BHP (512 21)
            ['code' => '51221001', 'name' => 'HPP Obat Poli Spesialis Utama', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51221002', 'name' => 'HPP Obat Poli Spesialis Umum & BPJS', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51221003', 'name' => 'HPP Obat Depo Farmasi Rawat Darurat', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51221004', 'name' => 'HPP Obat Depo Farmasi Rawat Inap Utama', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51221005', 'name' => 'HPP Obat Depo Farmasi Rawat Inap Umum & BPJS', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51221011', 'name' => 'HPP Alkes Poli Spesialis Utama', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51221012', 'name' => 'HPP Alkes Poli Spesialis Umum & BPJS', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51221013', 'name' => 'HPP Alkes Depo Farmasi Rawat Darurat', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51221014', 'name' => 'HPP Alkes Depo Farmasi Rawat Inap Utama', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51221015', 'name' => 'HPP Alkes Depo Farmasi Rawat Inap Umum & BPJS', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51221021', 'name' => 'HPP BHP Poli Spesialis Utama', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51221022', 'name' => 'HPP BHP Poli Spesialis Umum & BPJS', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51221023', 'name' => 'HPP BHP Depo Farmasi Rawat Darurat', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51221024', 'name' => 'HPP BHP Depo Farmasi Rawat Inap Utama', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51221025', 'name' => 'HPP BHP Depo Farmasi Rawat Inap Umum & BPJS', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51221026', 'name' => 'Jasa Apoteker dan Asisten Apoteker', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            
            // Laboratorium (512 22)
            ['code' => '51222001', 'name' => 'HPP Penggunaan Reagen Laboratorium', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51222002', 'name' => 'Jasa Analis Laboratorium', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            
            // Radiologi (512 23)
            ['code' => '51223001', 'name' => 'HPP Reagen Film Radiologi', 'cost_type' => 'variable', 'allocation_category' => 'bhp_medis'],
            ['code' => '51223002', 'name' => 'Tunjangan Bahaya Radiasi (TBR)', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            
            // Ruang OK (512 24)
            ['code' => '51224007', 'name' => 'Jasa Operator OK Ringan', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51224008', 'name' => 'Jasa Operator OK Sedang', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51224009', 'name' => 'Jasa Operator OK Berat', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51224010', 'name' => 'Jasa Anestesia (30% Operator)', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51224011', 'name' => 'Jasa Penata Anestesia (10% Operator)', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51224012', 'name' => 'Jasa Penata Bedah (10% Operator)', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            
            // Gizi (512 25)
            ['code' => '51225001', 'name' => 'HPP Gizi', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            
            // Medical Check Up (512 26)
            ['code' => '51226001', 'name' => 'Jasa Pemeriksaan Dokter MCU', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51226002', 'name' => 'Jasa Perawat MCU', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '51226003', 'name' => 'Biaya Administrasi MCU', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            
            // ============================================
            // 5.3 BIAYA ADMINISTRASI UMUM - GAJI & KESEJAHTERAAN (52)
            // ============================================
            
            // Gaji dan Upah (521)
            ['code' => '52100001', 'name' => 'Gaji Karyawan', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            ['code' => '52100002', 'name' => 'Lembur', 'cost_type' => 'variable', 'allocation_category' => 'gaji'],
            ['code' => '52100003', 'name' => 'Tunjangan Tetap', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            ['code' => '52100004', 'name' => 'Tunjangan Transport', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            ['code' => '52100005', 'name' => 'THR', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            
            // Kesejahteraan Karyawan (522)
            ['code' => '52200001', 'name' => 'Iuran BPJS Kesehatan', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            ['code' => '52200002', 'name' => 'Iuran JKK, JKM BPJS Ketenagakerjaan', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            ['code' => '52200003', 'name' => 'Rekreasi dan Olah Raga', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '52200004', 'name' => 'Biaya Training / Pelatihan', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '52200005', 'name' => 'Bantuan Pengobatan', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '52200006', 'name' => 'Sumbangan kepada Karyawan, Reward Kerja', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '52200007', 'name' => 'Beasiswa dan Tugas Belajar', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '52200008', 'name' => 'Biaya Kesejahteraan Lain-Lain', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            
            // ============================================
            // 5.4 BIAYA LAINNYA (523, 524)
            // ============================================
            
            // Perjalanan Dinas (523)
            ['code' => '52300001', 'name' => 'Biaya Transportasi Perjalanan Dinas Dalam Negeri', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '52300002', 'name' => 'Biaya Akomodasi Perjalanan Dinas Dalam Negeri', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '52300003', 'name' => 'UPD Dalam Negeri', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '52300004', 'name' => 'Biaya Transportasi Perjalanan Dinas Luar Negeri', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '52300005', 'name' => 'Biaya Akomodasi Perjalanan Dinas Luar Negeri', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '52300006', 'name' => 'UPD Luar Negeri', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            
            // Perlengkapan Kerja (524)
            ['code' => '52400001', 'name' => 'Alat Tulis Kantor', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '52400002', 'name' => 'Barang Cetakan', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '52400003', 'name' => 'Computer Supplies', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '52400004', 'name' => 'Seragam Kerja', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            ['code' => '52400005', 'name' => 'Biaya Meeting & Convention', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '52400006', 'name' => 'Perlengkapan Kerja Lain-Lain', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            
            // ============================================
            // 5.5 BIAYA UMUM (53)
            // ============================================
            
            // Perawatan & Perbaikan (531)
            ['code' => '53100001', 'name' => 'Biaya Perawatan Rutin Aktiva Tetap Gol. I & II', 'cost_type' => 'semi_variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53100002', 'name' => 'Biaya Perbaikan Aktiva Tetap Golongan I & II', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53100003', 'name' => 'Biaya Perawatan Rutin Bangunan', 'cost_type' => 'semi_variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53100004', 'name' => 'Biaya Perbaikan Bangunan', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            
            // BBM & Elpiji (532)
            ['code' => '53200001', 'name' => 'BBM Kendaraan', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53200002', 'name' => 'BBM Genset', 'cost_type' => 'semi_variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53200003', 'name' => 'Gas Elpiji', 'cost_type' => 'variable', 'allocation_category' => 'bhp_non_medis'],
            
            // Biaya-biaya Umum (533)
            ['code' => '53300001', 'name' => 'Biaya Pemakaian Listrik PLN', 'cost_type' => 'semi_variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53300002', 'name' => 'Biaya Pemakaian Air PDAM', 'cost_type' => 'semi_variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53300003', 'name' => 'Biaya Rekening Telepon dan Internet', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '53300004', 'name' => 'Biaya Kebersihan Lingkungan', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53300005', 'name' => 'Iuran Keanggotaan Organisasi', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '53300006', 'name' => 'Biaya Kerugian Piutang', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53300007', 'name' => 'Beban Biaya Sewa Alat', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '53300008', 'name' => 'Biaya Umum Lainnya', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            
            // Promosi & Penjualan (534)
            ['code' => '53400001', 'name' => 'Biaya Promosi', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53400002', 'name' => 'Biaya Pengiriman Barang & Dokumen', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53400003', 'name' => 'Biaya Penggunaan Meterai', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53400004', 'name' => 'Biaya Pemasaran', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53400005', 'name' => 'Biaya Promosi Lainnya', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            
            // Pajak & Retribusi (535)
            ['code' => '53500001', 'name' => 'PPh Pasal 25 Rampung', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '53500002', 'name' => 'PPN Masukan', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53500003', 'name' => 'Pajak Bumi dan Bangunan', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '53500004', 'name' => 'Pajak Pembangunan I', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '53500005', 'name' => 'Biaya Perijinan', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '53500006', 'name' => 'Retribusi Penerangan Jalan', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '53500007', 'name' => 'Retribusi Sampah', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '53500008', 'name' => 'Pajak dan Retribusi Lainnya', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            
            // Asuransi & Dana Pensiun (536)
            ['code' => '53600001', 'name' => 'Asuransi Kendaraan', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '53600002', 'name' => 'Asuransi Kebakaran dan Gempa Bumi', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '53600003', 'name' => 'Asuransi Kecelakaan Diri (Personal Accident)', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '53600004', 'name' => 'Asuransi Kesehatan', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '53600005', 'name' => 'JHT 3,7% BPJS Ketenagakerjaan', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            ['code' => '53600006', 'name' => 'JPK 4,0% BPJS Kesehatan', 'cost_type' => 'fixed', 'allocation_category' => 'gaji'],
            
            // Jasa Profesional (537)
            ['code' => '53700001', 'name' => 'Jasa Legal', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53700002', 'name' => 'Jasa Pendampingan / Jasa Manajemen', 'cost_type' => 'fixed', 'allocation_category' => 'lain_lain'],
            ['code' => '53700003', 'name' => 'Jasa Audit', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53700004', 'name' => 'Jasa Appraisal', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53700005', 'name' => 'Jasa Outsourcing', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            
            // Donasi & Sumbangan (538)
            ['code' => '53800001', 'name' => 'Donasi kepada Pihak III', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53800002', 'name' => 'Sumbangan kepada Pihak III', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53800003', 'name' => 'Representatif', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            ['code' => '53800004', 'name' => 'CSR', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
            
            // ============================================
            // 5.6 DEPRESIASI & AMORTISASI (54)
            // ============================================
            
            // Depresiasi (541)
            ['code' => '54100001', 'name' => 'Depresiasi Aktiva Tetap Golongan I', 'cost_type' => 'fixed', 'allocation_category' => 'depresiasi'],
            ['code' => '54100002', 'name' => 'Depresiasi Aktiva Tetap Golongan II', 'cost_type' => 'fixed', 'allocation_category' => 'depresiasi'],
            ['code' => '54100003', 'name' => 'Depresiasi Aktiva Tetap Golongan III', 'cost_type' => 'fixed', 'allocation_category' => 'depresiasi'],
            ['code' => '54100004', 'name' => 'Depresiasi Aktiva Tetap Golongan Bangunan', 'cost_type' => 'fixed', 'allocation_category' => 'depresiasi'],
            
            // Amortisasi (542)
            ['code' => '54200001', 'name' => 'Biaya Pengelolaan Usaha', 'cost_type' => 'fixed', 'allocation_category' => 'depresiasi'],
            ['code' => '54200002', 'name' => 'Amortisasi Biaya Kerjasama Operasi', 'cost_type' => 'fixed', 'allocation_category' => 'depresiasi'],
            
            // ============================================
            // 5.7 PEMBEBANAN DARI DEPARTEMEN PENUNJANG (599)
            // ============================================
            
            ['code' => '59900001', 'name' => 'Pembebanan dari Sub Dept', 'cost_type' => 'variable', 'allocation_category' => 'lain_lain'],
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
