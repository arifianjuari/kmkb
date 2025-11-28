<?php

namespace App\Http\Controllers\Concerns;

trait BlocksObserver
{
    /**
     * Block observer from write operations
     *
     * @param string $action
     * @return void
     */
    protected function blockObserver(string $action = 'mengubah'): void
    {
        if (auth()->user()?->isObserver()) {
            abort(403, "Observer tidak memiliki akses untuk {$action} data.");
        }
    }
}

