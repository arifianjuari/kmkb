<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SimrsController extends Controller
{
    /**
     * Display the master barang view
     *
     * @return \Illuminate\View\View
     */
    public function masterBarang()
    {
        return view('simrs.master-barang');
    }
    
    /**
     * Display the tindakan rawat jalan view
     *
     * @return \Illuminate\View\View
     */
    public function tindakanRawatJalanView()
    {
        return view('simrs.tindakan-rawat-jalan');
    }
    
    /**
     * Display the tindakan rawat inap view
     *
     * @return \Illuminate\View\View
     */
    public function tindakanRawatInapView()
    {
        return view('simrs.tindakan-rawat-inap');
    }
    
    /**
     * Display the laboratorium view
     *
     * @return \Illuminate\View\View
     */
    public function laboratorium()
    {
        return view('simrs.laboratorium');
    }
    
    /**
     * Display the radiologi view
     *
     * @return \Illuminate\View\View
     */
    public function radiologi()
    {
        return view('simrs.radiologi');
    }
    
    /**
     * Display the operasi view
     *
     * @return \Illuminate\View\View
     */
    public function operasi()
    {
        return view('simrs.operasi');
    }
    
    /**
     * Display the kamar view
     *
     * @return \Illuminate\View\View
     */
    public function kamarView()
    {
        return view('simrs.kamar');
    }
}
