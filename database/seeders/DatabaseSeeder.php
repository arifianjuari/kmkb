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
            CostCentersTableSeeder::class, // Harus sebelum CostReferencesTableSeeder karena ada FK
            ExpenseCategoriesTableSeeder::class, // Untuk GL Expenses
            AllocationDriversTableSeeder::class, // Untuk Driver Statistics dan Allocation Maps
            CostReferencesTableSeeder::class,
            UsersTableSeeder::class,
            JknCbgCodeSeeder::class,
            ClinicalPathwaysTableSeeder::class,
            PathwayStepsTableSeeder::class,
            PatientCasesTableSeeder::class,
            CaseDetailsTableSeeder::class,
            ReferencesTableSeeder::class,
            // Seeders untuk Unit Costing (tidak langsung ke unit_cost_calculations)
            GlExpensesTableSeeder::class, // Data pengeluaran per cost center
            DriverStatisticsTableSeeder::class, // Data driver per cost center
            AllocationMapsTableSeeder::class, // Mapping alokasi
            ServiceVolumesTableSeeder::class, // Volume layanan per cost reference
        ]);
    }
}
