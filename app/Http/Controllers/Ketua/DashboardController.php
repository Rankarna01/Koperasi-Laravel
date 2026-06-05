<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    public function index()
    {
        $data = $this->dashboardService->getKetuaDashboard();
        return view('ketua.dashboard', $data);
    }
}
