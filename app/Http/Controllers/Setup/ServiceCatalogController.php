<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceCatalogController extends Controller
{
    public function simrsLinked()
    {
        return view('setup.service-catalog.simrs-linked', [
            'title' => 'SIMRS-linked Items',
            'message' => 'Fitur untuk melihat dan mengelola item yang terhubung dengan SIMRS sedang dalam tahap pengembangan.'
        ]);
    }

    public function importExport()
    {
        return view('setup.service-catalog.import-export', [
            'title' => 'Import/Export Service Catalog',
            'message' => 'Fitur untuk import dan export service catalog secara bulk sedang dalam tahap pengembangan.'
        ]);
    }
}

