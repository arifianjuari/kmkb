<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CostReference;

class BackfillCostReferenceCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cost-references:backfill-category {--dry-run : Only show what would be updated without saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill category column for existing cost_references based on SIMRS source information';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info('Starting backfill of cost_references.category' . ($dryRun ? ' (dry run)' : ''));

        $query = CostReference::query()
            ->whereNull('category')
            ->where('is_synced_from_simrs', true);

        $total = $query->count();

        if ($total === 0) {
            $this->info('No records found that require backfill.');
            return Command::SUCCESS;
        }

        $this->info("Found {$total} records to evaluate for category backfill.");

        $updated = 0;

        $query->chunkById(200, function ($batch) use (&$updated, $dryRun) {
            /** @var \App\Models\CostReference $reference */
            foreach ($batch as $reference) {
                $category = $this->inferCategoryFromSource($reference->source);

                if (!$category) {
                    continue;
                }

                if ($dryRun) {
                    $this->line("Would set category='{$category}' for ID={$reference->id} ({$reference->service_code})");
                } else {
                    $reference->category = $category;
                    $reference->save();
                }

                $updated++;
            }
        });

        if ($dryRun) {
            $this->info("Dry run completed. {$updated} records would be updated.");
        } else {
            $this->info("Backfill completed. {$updated} records updated.");
        }

        return Command::SUCCESS;
    }

    /**
     * Infer category value from the source string.
     *
     * @param string|null $source
     * @return string|null
     */
    protected function inferCategoryFromSource(?string $source): ?string
    {
        if (!$source) {
            return null;
        }

        $normalized = strtolower($source);

        // Exact or contains-based heuristics based on current source patterns
        if ($normalized === 'simrs' || str_contains($normalized, 'barang')) {
            return 'barang';
        }

        if (str_contains($normalized, 'tindakan rawat jalan')) {
            return 'tindakan_rj';
        }

        if (str_contains($normalized, 'tindakan rawat inap')) {
            return 'tindakan_ri';
        }

        if (str_contains($normalized, 'laboratorium')) {
            return 'laboratorium';
        }

        if (str_contains($normalized, 'radiologi')) {
            return 'radiologi';
        }

        if (str_contains($normalized, 'operasi')) {
            return 'operasi';
        }

        if (str_contains($normalized, 'kamar')) {
            return 'kamar';
        }

        return null;
    }
}


