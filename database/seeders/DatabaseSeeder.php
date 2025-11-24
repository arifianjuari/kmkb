<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            HospitalsTableSeeder::class,
            CostReferencesTableSeeder::class,
            UsersTableSeeder::class,
            ClinicalPathwaysTableSeeder::class,
            PathwayStepsTableSeeder::class,
            PatientCasesTableSeeder::class,
            CaseDetailsTableSeeder::class,
            ReferencesTableSeeder::class,
        ]);
    }
}
