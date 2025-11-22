<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hospital;

class HospitalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Hospital::updateOrCreate(
            ['code' => 'RSBB'],
            [
                'name' => 'Rumah Sakit Bhayangkara',
                'logo_path' => 'rsbb-logo.png',
                'theme_color' => '#2563eb',
                'address' => 'Jl. Raya Jakarta-Bogor No. 1, Jakarta',
                'contact' => '(021) 12345678',
                'is_active' => true,
            ]
        );
    }
}
