<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\UnitOfMeasurement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitOfMeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            // Area units - for allocation
            ['code' => 'm2', 'name' => 'Meter Persegi', 'symbol' => 'm²', 'category' => 'area', 'context' => 'allocation'],
            
            // Volume units - for allocation
            ['code' => 'kwh', 'name' => 'Kilowatt Hour', 'symbol' => 'kWh', 'category' => 'volume', 'context' => 'allocation'],
            ['code' => 'm3', 'name' => 'Meter Kubik', 'symbol' => 'm³', 'category' => 'volume', 'context' => 'allocation'],
            ['code' => 'liter', 'name' => 'Liter', 'symbol' => 'L', 'category' => 'volume', 'context' => 'both'],
            
            // Weight units - for allocation and service
            ['code' => 'kg', 'name' => 'Kilogram', 'symbol' => 'kg', 'category' => 'weight', 'context' => 'both'],
            ['code' => 'gram', 'name' => 'Gram', 'symbol' => 'g', 'category' => 'weight', 'context' => 'service'],
            ['code' => 'mg', 'name' => 'Miligram', 'symbol' => 'mg', 'category' => 'weight', 'context' => 'service'],
            
            // Count units - for allocation
            ['code' => 'orang', 'name' => 'Orang', 'symbol' => 'org', 'category' => 'count', 'context' => 'both'],
            ['code' => 'pasien', 'name' => 'Pasien', 'symbol' => 'psn', 'category' => 'count', 'context' => 'both'],
            ['code' => 'kunjungan', 'name' => 'Kunjungan', 'symbol' => 'knj', 'category' => 'count', 'context' => 'allocation'],
            ['code' => 'tt', 'name' => 'Tempat Tidur', 'symbol' => 'TT', 'category' => 'count', 'context' => 'allocation'],
            ['code' => 'kamar', 'name' => 'Kamar', 'symbol' => 'kmr', 'category' => 'count', 'context' => 'allocation'],
            
            // Time units - for both
            ['code' => 'hari', 'name' => 'Hari', 'symbol' => 'hr', 'category' => 'time', 'context' => 'both'],
            ['code' => 'jam', 'name' => 'Jam', 'symbol' => 'jam', 'category' => 'time', 'context' => 'both'],
            ['code' => 'menit', 'name' => 'Menit', 'symbol' => 'min', 'category' => 'time', 'context' => 'both'],
            
            // Service units - for cost references
            ['code' => 'tindakan', 'name' => 'Tindakan', 'symbol' => 'tdk', 'category' => 'service', 'context' => 'service'],
            ['code' => 'pemeriksaan', 'name' => 'Pemeriksaan', 'symbol' => 'pmx', 'category' => 'service', 'context' => 'service'],
            ['code' => 'sample', 'name' => 'Sample', 'symbol' => 'spl', 'category' => 'service', 'context' => 'service'],
            ['code' => 'test', 'name' => 'Test', 'symbol' => 'tst', 'category' => 'service', 'context' => 'service'],
            ['code' => 'kali', 'name' => 'Kali', 'symbol' => 'x', 'category' => 'count', 'context' => 'service'],
            ['code' => 'paket', 'name' => 'Paket', 'symbol' => 'pkt', 'category' => 'count', 'context' => 'service'],
            
            // Drug/item units - for service
            ['code' => 'tablet', 'name' => 'Tablet', 'symbol' => 'tab', 'category' => 'count', 'context' => 'service'],
            ['code' => 'kapsul', 'name' => 'Kapsul', 'symbol' => 'kps', 'category' => 'count', 'context' => 'service'],
            ['code' => 'ampul', 'name' => 'Ampul', 'symbol' => 'amp', 'category' => 'count', 'context' => 'service'],
            ['code' => 'vial', 'name' => 'Vial', 'symbol' => 'vial', 'category' => 'count', 'context' => 'service'],
            ['code' => 'botol', 'name' => 'Botol', 'symbol' => 'btl', 'category' => 'count', 'context' => 'service'],
            ['code' => 'tube', 'name' => 'Tube', 'symbol' => 'tube', 'category' => 'count', 'context' => 'service'],
            ['code' => 'sachet', 'name' => 'Sachet', 'symbol' => 'sct', 'category' => 'count', 'context' => 'service'],
            ['code' => 'strip', 'name' => 'Strip', 'symbol' => 'str', 'category' => 'count', 'context' => 'service'],
            ['code' => 'box', 'name' => 'Box', 'symbol' => 'box', 'category' => 'count', 'context' => 'service'],
            ['code' => 'pcs', 'name' => 'Pieces', 'symbol' => 'pcs', 'category' => 'count', 'context' => 'service'],
            ['code' => 'unit', 'name' => 'Unit', 'symbol' => 'unit', 'category' => 'count', 'context' => 'both'],
            ['code' => 'set', 'name' => 'Set', 'symbol' => 'set', 'category' => 'count', 'context' => 'both'],
            
            // Medical specific
            ['code' => 'ml', 'name' => 'Mililiter', 'symbol' => 'mL', 'category' => 'volume', 'context' => 'service'],
            ['code' => 'cc', 'name' => 'Cubic Centimeter', 'symbol' => 'cc', 'category' => 'volume', 'context' => 'service'],
            ['code' => 'iu', 'name' => 'International Unit', 'symbol' => 'IU', 'category' => 'count', 'context' => 'service'],
        ];

        // Get all hospitals
        $hospitals = Hospital::all();

        foreach ($hospitals as $hospital) {
            foreach ($units as $unit) {
                UnitOfMeasurement::firstOrCreate(
                    [
                        'hospital_id' => $hospital->id,
                        'code' => $unit['code'],
                    ],
                    [
                        'name' => $unit['name'],
                        'symbol' => $unit['symbol'],
                        'category' => $unit['category'],
                        'context' => $unit['context'],
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('Unit of Measurement seeder completed. Added ' . count($units) . ' units per hospital.');
    }
}
