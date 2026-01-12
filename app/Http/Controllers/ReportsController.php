<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function __construct()
    {
        
    }

    /**
     * Display the reports index page.
     */
    public function index()
    {
        return view('reports.reportsIndex');
    }
}
