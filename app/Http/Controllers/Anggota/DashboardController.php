<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    public function index()
    {
        $user = auth()->user();
        $anggota = $user->anggota;

        if (!$anggota) {
            return redirect()->route('anggota.pendaftaran');
        }

        $data = $this->dashboardService->getAnggotaDashboard($anggota);
        $data['anggota'] = $anggota;
        $data['user'] = $user;
        return view('anggota.dashboard', $data);
    }
}
