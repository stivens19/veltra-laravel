<?php

namespace Veltra\VeltraLaravel\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class DemoDashboardController extends Controller
{
    /**
     * Show the VeltraUI interactive demo dashboard.
     */
    public function index(): View
    {
        return view('veltra::demo.dashboard');
    }
}

