<?php

namespace Database\Seeders;

use App\Models\AllocationDriver;
use App\Models\AllocationResult;
use App\Models\ClinicalPathway;
use App\Models\CostCenter;
use App\Models\CostReference;
use App\Models\DriverStatistic;
use App\Models\ExpenseCategory;
use App\Models\GlExpense;
use App\Models\Hospital;
use App\Models\PatientCase;
use App\Models\ServiceVolume;
use App\Models\TariffClass;
use App\Models\UnitCostCalculation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder ini menghasilkan data komprehensif untuk menampilkan chart di dashboard.
     * Data mencakup 6 bulan terakhir dengan variasi yang realistis.
     */
    public function run(): void
    {
        $hospital = Hospital::first();
        if (!$hospital) {
            $hospital = Hospital::create([
                'code' => 'HOSP1',
                'name' => 'Rumah Sakit Contoh',
                'is_active' => true,
            ]);
        }

        $user = User::where('hospital_id', $hospital->id)->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin Dashboard',
                'email' => 'admin@dashboard.test',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'hospital_id' => $hospital->id,
            ]);
        }

        $this->command->info('Membuat data untuk dashboard...');

        // 1. Pastikan ada Cost Centers
        $this->seedCostCenters($hospital->id);

        // 2. Pastikan ada Expense Categories
        $this->seedExpenseCategories($hospital->id);

        // 3. Pastikan ada Allocation Drivers
        $this->seedAllocationDrivers($hospital->id);

        // 4. Pastikan ada Tariff Classes
        $this->seedTariffClasses($hospital->id);

        // 5. Pastikan ada Cost References dengan tarif
        $this->seedCostReferences($hospital->id);

        // 6. Pastikan ada Clinical Pathways dengan standard_los
        $this->seedClinicalPathways($hospital->id, $user->id);

        // 7. Generate data untuk 6 bulan terakhir
        $this->seedHistoricalData($hospital->id, $user->id);

        $this->command->info('Data dashboard berhasil dibuat!');
    }

    /**
     * Seed Cost Centers
     */
    private function seedCostCenters($hospitalId)
    {
        $costCenters = [
            ['code' => 'CC001', 'name' => 'Rawat Inap', 'type' => 'revenue'],
            ['code' => 'CC002', 'name' => 'Rawat Jalan', 'type' => 'revenue'],
            ['code' => 'CC003', 'name' => 'IGD', 'type' => 'revenue'],
            ['code' => 'CC004', 'name' => 'Laboratorium', 'type' => 'revenue'],
            ['code' => 'CC005', 'name' => 'Radiologi', 'type' => 'revenue'],
            ['code' => 'CC006', 'name' => 'Farmasi', 'type' => 'revenue'],
            ['code' => 'CC007', 'name' => 'Operasi', 'type' => 'revenue'],
            ['code' => 'CC101', 'name' => 'Administrasi', 'type' => 'support'],
            ['code' => 'CC102', 'name' => 'HRD', 'type' => 'support'],
            ['code' => 'CC103', 'name' => 'IT', 'type' => 'support'],
            ['code' => 'CC104', 'name' => 'Housekeeping', 'type' => 'support'],
        ];

        foreach ($costCenters as $cc) {
            CostCenter::updateOrCreate(
                ['code' => $cc['code'], 'hospital_id' => $hospitalId],
                array_merge($cc, [
                    'hospital_id' => $hospitalId,
                    'is_active' => true,
                ])
            );
        }
    }

    /**
     * Seed Expense Categories
     */
    private function seedExpenseCategories($hospitalId)
    {
        $categories = [
            [
                'account_code' => 'SAL',
                'account_name' => 'Gaji & Tunjangan',
                'cost_type' => 'fixed',
                'allocation_category' => 'gaji',
            ],
            [
                'account_code' => 'MED',
                'account_name' => 'Obat & Bahan Medis',
                'cost_type' => 'variable',
                'allocation_category' => 'bhp_medis',
            ],
            [
                'account_code' => 'EQU',
                'account_name' => 'Peralatan Medis',
                'cost_type' => 'fixed',
                'allocation_category' => 'depresiasi',
            ],
            [
                'account_code' => 'UTL',
                'account_name' => 'Listrik & Air',
                'cost_type' => 'variable',
                'allocation_category' => 'lain_lain',
            ],
            [
                'account_code' => 'MNT',
                'account_name' => 'Pemeliharaan',
                'cost_type' => 'semi_variable',
                'allocation_category' => 'lain_lain',
            ],
        ];

        foreach ($categories as $cat) {
            ExpenseCategory::updateOrCreate(
                ['account_code' => $cat['account_code'], 'hospital_id' => $hospitalId],
                array_merge($cat, [
                    'hospital_id' => $hospitalId,
                    'is_active' => true,
                ])
            );
        }
    }

    /**
     * Seed Allocation Drivers
     */
    private function seedAllocationDrivers($hospitalId)
    {
        $drivers = [
            ['name' => 'Jumlah Pasien', 'unit_measurement' => 'pasien', 'description' => 'Driver berdasarkan jumlah pasien'],
            ['name' => 'Jam Layanan', 'unit_measurement' => 'jam', 'description' => 'Driver berdasarkan jam layanan'],
            ['name' => 'Luas Ruangan', 'unit_measurement' => 'm²', 'description' => 'Driver berdasarkan luas ruangan'],
            ['name' => 'Jumlah Karyawan', 'unit_measurement' => 'orang', 'description' => 'Driver berdasarkan jumlah karyawan'],
        ];

        foreach ($drivers as $driver) {
            AllocationDriver::updateOrCreate(
                ['name' => $driver['name'], 'hospital_id' => $hospitalId],
                array_merge($driver, ['hospital_id' => $hospitalId])
            );
        }
    }

    /**
     * Seed Tariff Classes
     */
    private function seedTariffClasses($hospitalId)
    {
        $classes = [
            ['name' => 'VIP', 'code' => 'VIP'],
            ['name' => 'Kelas I', 'code' => 'I'],
            ['name' => 'Kelas II', 'code' => 'II'],
            ['name' => 'Kelas III', 'code' => 'III'],
        ];

        foreach ($classes as $class) {
            TariffClass::updateOrCreate(
                ['code' => $class['code'], 'hospital_id' => $hospitalId],
                array_merge($class, ['hospital_id' => $hospitalId])
            );
        }
    }

    /**
     * Seed Cost References dengan tarif
     */
    private function seedCostReferences($hospitalId)
    {
        $revenueCenters = CostCenter::where('hospital_id', $hospitalId)
            ->where('type', 'revenue')
            ->get();

        $expenseCategories = ExpenseCategory::where('hospital_id', $hospitalId)->get();

        $services = [
            ['code' => 'LAB001', 'name' => 'Hematologi Lengkap', 'base_cost' => 50000, 'tariff' => 75000],
            ['code' => 'LAB002', 'name' => 'Kimia Darah', 'base_cost' => 75000, 'tariff' => 120000],
            ['code' => 'RAD001', 'name' => 'Foto Thorax', 'base_cost' => 100000, 'tariff' => 150000],
            ['code' => 'RAD002', 'name' => 'USG Abdomen', 'base_cost' => 200000, 'tariff' => 300000],
            ['code' => 'OPR001', 'name' => 'Appendektomi', 'base_cost' => 5000000, 'tariff' => 7500000],
            ['code' => 'OPR002', 'name' => 'Sectio Caesarea', 'base_cost' => 8000000, 'tariff' => 12000000],
            ['code' => 'FAR001', 'name' => 'Antibiotik Amoxicillin', 'base_cost' => 5000, 'tariff' => 10000],
            ['code' => 'FAR002', 'name' => 'Paracetamol', 'base_cost' => 2000, 'tariff' => 5000],
        ];

        foreach ($services as $service) {
            $costCenter = $revenueCenters->random();
            $expenseCategory = $expenseCategories->random();

            CostReference::updateOrCreate(
                ['service_code' => $service['code'], 'hospital_id' => $hospitalId],
                [
                    'service_description' => $service['name'],
                    'standard_cost' => $service['base_cost'],
                    'selling_price_unit' => $service['tariff'],
                    'selling_price_total' => $service['tariff'],
                    'unit' => 'unit',
                    'source' => 'manual',
                    'hospital_id' => $hospitalId,
                    'cost_center_id' => $costCenter->id,
                    'expense_category_id' => $expenseCategory->id,
                    'is_bundle' => false,
                ]
            );
        }
    }

    /**
     * Seed Clinical Pathways dengan standard_los
     */
    private function seedClinicalPathways($hospitalId, $userId)
    {
        $pathways = [
            [
                'name' => 'Appendektomi',
                'description' => 'Clinical pathway untuk kasus appendektomi',
                'diagnosis_code' => 'K35',
                'standard_los' => 3,
            ],
            [
                'name' => 'Pneumonia',
                'description' => 'Clinical pathway untuk kasus pneumonia',
                'diagnosis_code' => 'J18',
                'standard_los' => 5,
            ],
            [
                'name' => 'Sectio Caesarea',
                'description' => 'Clinical pathway untuk sectio caesarea',
                'diagnosis_code' => 'O82',
                'standard_los' => 4,
            ],
            [
                'name' => 'Diabetes Mellitus Type 2',
                'description' => 'Clinical pathway untuk DM tipe 2',
                'diagnosis_code' => 'E11',
                'standard_los' => 6,
            ],
            [
                'name' => 'Hipertensi',
                'description' => 'Clinical pathway untuk hipertensi',
                'diagnosis_code' => 'I10',
                'standard_los' => 3,
            ],
        ];

        foreach ($pathways as $pathway) {
            $cp = ClinicalPathway::updateOrCreate(
                ['name' => $pathway['name'], 'hospital_id' => $hospitalId],
                array_merge($pathway, [
                    'version' => '1.0',
                    'effective_date' => now()->subMonths(6),
                    'status' => 'active',
                    'created_by' => $userId,
                    'hospital_id' => $hospitalId,
                ])
            );

            // Update standard_los jika field ada di database
            if (DB::getSchemaBuilder()->hasColumn('clinical_pathways', 'standard_los')) {
                DB::table('clinical_pathways')
                    ->where('id', $cp->id)
                    ->update(['standard_los' => $pathway['standard_los']]);
            }
        }
    }

    /**
     * Seed historical data untuk 6 bulan terakhir
     */
    private function seedHistoricalData($hospitalId, $userId)
    {
        $now = Carbon::now();
        $pathways = ClinicalPathway::where('hospital_id', $hospitalId)->get();
        $costCenters = CostCenter::where('hospital_id', $hospitalId)->get();
        $revenueCenters = $costCenters->where('type', 'revenue');
        $supportCenters = $costCenters->where('type', 'support');
        $costReferences = CostReference::where('hospital_id', $hospitalId)->get();
        $allocationDrivers = AllocationDriver::where('hospital_id', $hospitalId)->get();
        $expenseCategories = ExpenseCategory::where('hospital_id', $hospitalId)->get();
        $tariffClasses = TariffClass::where('hospital_id', $hospitalId)->get();

        // Generate data untuk 6 bulan terakhir
        for ($monthOffset = 5; $monthOffset >= 0; $monthOffset--) {
            $date = $now->copy()->subMonths($monthOffset);
            $year = $date->year;
            $month = $date->month;

            $this->command->info("Generating data for {$year}-{$month}...");

            // 1. GL Expenses
            $this->seedGlExpenses($hospitalId, $year, $month, $costCenters, $expenseCategories);

            // 2. Driver Statistics
            $this->seedDriverStatistics($hospitalId, $year, $month, $costCenters, $allocationDrivers);

            // 3. Allocation Results
            $this->seedAllocationResults($hospitalId, $year, $month, $supportCenters, $revenueCenters);

            // 4. Service Volumes
            $this->seedServiceVolumes($hospitalId, $year, $month, $costReferences, $tariffClasses);

            // 5. Unit Cost Calculations
            $this->seedUnitCostCalculations($hospitalId, $year, $month, $costReferences);

            // 6. Patient Cases (dengan variasi compliance dan cost variance)
            $this->seedPatientCases($hospitalId, $userId, $year, $month, $pathways);
        }
    }

    /**
     * Seed GL Expenses
     */
    private function seedGlExpenses($hospitalId, $year, $month, $costCenters, $expenseCategories)
    {
        foreach ($costCenters as $costCenter) {
            foreach ($expenseCategories as $category) {
                // Random amount antara 1 juta sampai 50 juta
                $amount = rand(1000000, 50000000);

                GlExpense::updateOrCreate(
                    [
                        'hospital_id' => $hospitalId,
                        'period_year' => $year,
                        'period_month' => $month,
                        'cost_center_id' => $costCenter->id,
                        'expense_category_id' => $category->id,
                    ],
                    ['amount' => $amount]
                );
            }
        }
    }

    /**
     * Seed Driver Statistics
     */
    private function seedDriverStatistics($hospitalId, $year, $month, $costCenters, $allocationDrivers)
    {
        foreach ($costCenters as $costCenter) {
            foreach ($allocationDrivers as $driver) {
                // Random value berdasarkan jenis driver berdasarkan nama
                $driverName = strtolower($driver->name);
                if (strpos($driverName, 'pasien') !== false) {
                    $baseValue = rand(50, 500); // Jumlah pasien
                } elseif (strpos($driverName, 'jam') !== false || strpos($driverName, 'layanan') !== false) {
                    $baseValue = rand(100, 2000); // Jam layanan
                } elseif (strpos($driverName, 'luas') !== false || strpos($driverName, 'lantai') !== false) {
                    $baseValue = rand(100, 5000); // Luas ruangan
                } elseif (strpos($driverName, 'karyawan') !== false || strpos($driverName, 'fte') !== false) {
                    $baseValue = rand(5, 100); // Jumlah karyawan
                } else {
                    $baseValue = rand(10, 1000); // Default
                }

                DriverStatistic::updateOrCreate(
                    [
                        'hospital_id' => $hospitalId,
                        'period_year' => $year,
                        'period_month' => $month,
                        'cost_center_id' => $costCenter->id,
                        'allocation_driver_id' => $driver->id,
                    ],
                    ['value' => $baseValue]
                );
            }
        }
    }

    /**
     * Seed Allocation Results
     */
    private function seedAllocationResults($hospitalId, $year, $month, $supportCenters, $revenueCenters)
    {
        $step = 1;
        foreach ($supportCenters as $source) {
            foreach ($revenueCenters as $target) {
                // Random allocated amount antara 500 ribu sampai 10 juta
                $allocatedAmount = rand(500000, 10000000);

                AllocationResult::updateOrCreate(
                    [
                        'hospital_id' => $hospitalId,
                        'period_year' => $year,
                        'period_month' => $month,
                        'source_cost_center_id' => $source->id,
                        'target_cost_center_id' => $target->id,
                        'allocation_step' => $step,
                    ],
                    ['allocated_amount' => $allocatedAmount]
                );
            }
            $step++;
        }
    }

    /**
     * Seed Service Volumes
     */
    private function seedServiceVolumes($hospitalId, $year, $month, $costReferences, $tariffClasses)
    {
        foreach ($costReferences as $costRef) {
            foreach ($tariffClasses as $tariffClass) {
                // Random quantity antara 10 sampai 1000
                $quantity = rand(10, 1000);

                ServiceVolume::updateOrCreate(
                    [
                        'hospital_id' => $hospitalId,
                        'period_year' => $year,
                        'period_month' => $month,
                        'cost_reference_id' => $costRef->id,
                        'tariff_class_id' => $tariffClass->id,
                    ],
                    ['total_quantity' => $quantity]
                );
            }
        }
    }

    /**
     * Seed Unit Cost Calculations
     */
    private function seedUnitCostCalculations($hospitalId, $year, $month, $costReferences)
    {
        $versionLabel = "UC_{$year}_" . str_pad($month, 2, '0', STR_PAD_LEFT);

        foreach ($costReferences as $costRef) {
            $directMaterial = $costRef->standard_cost * 0.4;
            $directLabor = $costRef->standard_cost * 0.3;
            $indirectOverhead = $costRef->standard_cost * 0.3;
            $totalUnitCost = $directMaterial + $directLabor + $indirectOverhead;

            UnitCostCalculation::updateOrCreate(
                [
                    'hospital_id' => $hospitalId,
                    'period_year' => $year,
                    'period_month' => $month,
                    'cost_reference_id' => $costRef->id,
                    'version_label' => $versionLabel,
                ],
                [
                    'direct_cost_material' => $directMaterial,
                    'direct_cost_labor' => $directLabor,
                    'indirect_cost_overhead' => $indirectOverhead,
                    'total_unit_cost' => $totalUnitCost,
                ]
            );
        }
    }

    /**
     * Seed Patient Cases dengan variasi compliance dan cost variance
     */
    private function seedPatientCases($hospitalId, $userId, $year, $month, $pathways)
    {
        // Generate 10-30 cases per pathway per bulan
        foreach ($pathways as $pathway) {
            $numCases = rand(10, 30);

            for ($i = 0; $i < $numCases; $i++) {
                // Random admission date dalam bulan tersebut
                $admissionDate = Carbon::create($year, $month, rand(1, 28));
                
                // Standard LOS dari pathway (atau default 5)
                $standardLos = DB::getSchemaBuilder()->hasColumn('clinical_pathways', 'standard_los')
                    ? (DB::table('clinical_pathways')->where('id', $pathway->id)->value('standard_los') ?? 5)
                    : 5;

                // Actual LOS dengan variasi (bisa lebih atau kurang dari standard)
                $actualLos = $standardLos + rand(-2, 3);
                $actualLos = max(1, $actualLos); // Minimum 1 hari
                
                $dischargeDate = $admissionDate->copy()->addDays($actualLos);

                // Compliance percentage (70-100%, dengan beberapa kasus rendah)
                $compliance = rand(1, 10) <= 2 ? rand(40, 69) : rand(70, 100);

                // INA-CBG Tariff (random antara 2-10 juta)
                $inaCbgTariff = rand(2000000, 10000000);

                // Actual cost dengan variasi terhadap INA-CBG
                // Beberapa kasus defisit (lebih tinggi dari INA-CBG)
                $variancePercent = rand(1, 10) <= 3 
                    ? rand(10, 50) // Defisit 10-50%
                    : rand(-20, 20); // Surplus atau defisit kecil
                
                $actualTotalCost = $inaCbgTariff * (1 + $variancePercent / 100);
                $costVariance = $actualTotalCost - $inaCbgTariff;

                // Random INA-CBG code (50% JKN, 50% Non-JKN)
                $isJkn = rand(1, 2) === 1;
                $inaCbgCode = $isJkn ? 'CBG' . rand(100, 999) : null;

                PatientCase::create([
                    'patient_id' => 'PAT-' . $pathway->id . '-' . $year . $month . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'medical_record_number' => 'MRN-' . $year . $month . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'clinical_pathway_id' => $pathway->id,
                    'admission_date' => $admissionDate,
                    'discharge_date' => $dischargeDate,
                    'primary_diagnosis' => $pathway->diagnosis_code,
                    'ina_cbg_code' => $inaCbgCode,
                    'actual_total_cost' => round($actualTotalCost, 2),
                    'ina_cbg_tariff' => $isJkn ? $inaCbgTariff : 0,
                    'compliance_percentage' => round($compliance, 2),
                    'cost_variance' => round($costVariance, 2),
                    'input_by' => $userId,
                    'input_date' => $admissionDate->copy()->addDays(rand(0, 2)),
                    'hospital_id' => $hospitalId,
                ]);
            }
        }
    }
}

