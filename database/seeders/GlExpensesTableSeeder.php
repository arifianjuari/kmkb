<?php

namespace Database\Seeders;

use App\Models\GlExpense;
use App\Models\Hospital;
use App\Models\CostCenter;
use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class GlExpensesTableSeeder extends Seeder
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

        $this->command->info('Membuat data GL Expenses...');

        // Pastikan ada cost centers dan expense categories
        $costCenters = CostCenter::where('hospital_id', $hospital->id)->get();
        $expenseCategories = ExpenseCategory::where('hospital_id', $hospital->id)->get();

        if ($costCenters->isEmpty()) {
            $this->command->info('Membuat cost centers...');
            $this->call(CostCentersTableSeeder::class);
            $costCenters = CostCenter::where('hospital_id', $hospital->id)->get();
        }

        if ($expenseCategories->isEmpty()) {
            $this->command->info('Membuat expense categories...');
            $this->call(ExpenseCategoriesTableSeeder::class);
            $expenseCategories = ExpenseCategory::where('hospital_id', $hospital->id)->get();
        }

        // Generate data untuk 3 bulan terakhir
        $periods = [];
        $currentMonth = (int) date('m');
        $currentYear = (int) date('Y');
        
        for ($i = 2; $i >= 0; $i--) {
            $month = $currentMonth - $i;
            $year = $currentYear;
            if ($month <= 0) {
                $month += 12;
                $year -= 1;
            }
            $periods[] = ['month' => $month, 'year' => $year];
        }

        $created = 0;
        foreach ($periods as $period) {
            foreach ($costCenters as $costCenter) {
                // Setiap cost center memiliki beberapa expense categories
                $categoriesToUse = $expenseCategories->random(min(5, $expenseCategories->count()));
                
                foreach ($categoriesToUse as $category) {
                    // Generate amount berdasarkan tipe cost center dan kategori
                    $baseAmount = $this->getBaseAmount($costCenter, $category);
                    $amount = $baseAmount * (0.9 + (rand(0, 20) / 100)); // Variasi ±10%
                    
                    GlExpense::updateOrCreate(
                        [
                            'hospital_id' => $hospital->id,
                            'period_month' => $period['month'],
                            'period_year' => $period['year'],
                            'cost_center_id' => $costCenter->id,
                            'expense_category_id' => $category->id,
                        ],
                        [
                            'amount' => round($amount, 2),
                        ]
                    );
                    $created++;
                }
            }
        }

        $this->command->info("✓ GL Expenses created: {$created} records for " . count($periods) . " period(s)");
    }

    /**
     * Get base amount berdasarkan tipe cost center dan kategori expense
     */
    private function getBaseAmount($costCenter, $category): float
    {
        $base = 0;
        
        // Base amount berdasarkan tipe cost center
        if ($costCenter->type === 'revenue') {
            $base = match($costCenter->code) {
                'IGD' => 50000000,
                'RAWAT-INAP' => 200000000,
                'RAWAT-JALAN' => 150000000,
                'LAB' => 80000000,
                'RAD' => 100000000,
                'OK' => 120000000,
                'ICU', 'HCU', 'PICU', 'NICU' => 80000000,
                'FARMASI' => 60000000,
                default => 50000000,
            };
        } else {
            $base = match($costCenter->code) {
                'ADM' => 30000000,
                'SDM' => 40000000,
                'KEUANGAN' => 25000000,
                'IT' => 20000000,
                'MES' => 35000000,
                'LAUNDRY' => 15000000,
                'KEBERSIHAN' => 20000000,
                'KEAMANAN' => 10000000,
                default => 15000000,
            };
        }
        
        // Adjust berdasarkan kategori
        $multiplier = match($category->allocation_category) {
            'gaji' => 0.4,           // 40% untuk gaji
            'bhp_medis' => 0.3,      // 30% untuk BHP medis
            'bhp_non_medis' => 0.1,  // 10% untuk BHP non medis
            'depresiasi' => 0.1,     // 10% untuk depresiasi
            'lain_lain' => 0.1,      // 10% untuk lain-lain
            default => 0.1,
        };
        
        return $base * $multiplier;
    }
}


