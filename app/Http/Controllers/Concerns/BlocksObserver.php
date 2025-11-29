<?php

namespace App\Http\Controllers\Concerns;

trait BlocksObserver
{
    /**
     * Block observer/management_auditor from write operations
     *
     * @param string $action
     * @return void
     */
    protected function blockObserver(string $action = 'mengubah'): void
    {
        if (auth()->user()?->isObserver()) {
            abort(403, "Management Auditor tidak memiliki akses untuk {$action} data.");
        }
    }
}

