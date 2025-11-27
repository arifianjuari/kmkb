<?php

namespace App\Http\Controllers\Pathway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PathwayTemplateController extends Controller
{
    public function index()
    {
        return view('pathways.templates', [
            'title' => 'Pathway Templates',
            'message' => 'Fitur untuk mengelola template pathway (import/export) sedang dalam tahap pengembangan.'
        ]);
    }
}

