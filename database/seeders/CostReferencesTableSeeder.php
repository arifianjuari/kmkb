<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CostReferencesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        // Determine hospital scope (first hospital)
        $hospital = \App\Models\Hospital::first();
        $hospitalId = $hospital ? $hospital->id : 1;

        // Use upsert to make seeding idempotent on repeated runs
        DB::table('cost_references')->upsert([
            [
                'service_code' => 'LAB-HEM-CBC',
                'service_description' => 'Laboratorium - Hematologi Lengkap (CBC)',
                'standard_cost' => 75000,
                'unit' => 'tindakan',
                'source' => 'internal',
                'hospital_id' => $hospitalId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'service_code' => 'RAD-THORAX-PA',
                'service_description' => 'Radiologi - Foto Thorax PA',
                'standard_cost' => 120000,
                'unit' => 'tindakan',
                'source' => 'internal',
                'hospital_id' => $hospitalId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'service_code' => 'DRG-VISIT',
                'service_description' => 'Visite Dokter Spesialis',
                'standard_cost' => 100000,
                'unit' => 'kunjungan',
                'source' => 'internal',
                'hospital_id' => $hospitalId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['service_code'], ['service_description', 'standard_cost', 'unit', 'source', 'hospital_id', 'updated_at']);
    }
}
