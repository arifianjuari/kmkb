<?php

namespace Database\Seeders;

use App\Models\JknCbgCode;
use Illuminate\Database\Seeder;

class JknCbgCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cbgCodes = [
            [
                'code' => 'A01',
                'name' => 'Pneumonia usia dewasa',
                'description' => 'Pneumonia pada pasien usia dewasa',
                'service_type' => 'Rawat Inap',
                'severity_level' => 2,
                'grouping_version' => 'Grouper 6.x',
                'tariff' => 5000000,
                'is_active' => true,
            ],
            [
                'code' => 'B02',
                'name' => 'Fraktur tulang',
                'description' => 'Fraktur tulang dengan tindakan operasi',
                'service_type' => 'Rawat Jalan',
                'severity_level' => 3,
                'grouping_version' => 'Grouper 6.x',
                'tariff' => 7500000,
                'is_active' => true,
            ],
            [
                'code' => 'C03',
                'name' => 'Persalinan normal',
                'description' => 'Persalinan normal tanpa komplikasi',
                'service_type' => 'Rawat Inap',
                'severity_level' => 1,
                'grouping_version' => 'Grouper 6.x',
                'tariff' => 3000000,
                'is_active' => true,
            ],
            [
                'code' => 'D04',
                'name' => 'Cesarean section',
                'description' => 'Persalinan dengan operasi Caesar',
                'service_type' => 'Rawat Inap',
                'severity_level' => 2,
                'grouping_version' => 'Grouper 6.x',
                'tariff' => 8000000,
                'is_active' => true,
            ],
            [
                'code' => 'E05',
                'name' => 'Appendectomy',
                'description' => 'Operasi pengangkatan usus buntu',
                'service_type' => 'Rawat Inap',
                'severity_level' => 3,
                'grouping_version' => 'Grouper 6.x',
                'tariff' => 12000000,
                'is_active' => true,
            ],
        ];

        foreach ($cbgCodes as $cbgCode) {
            JknCbgCode::updateOrCreate(
                ['code' => $cbgCode['code']],
                $cbgCode
            );
        }
    }
}
