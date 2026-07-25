<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Dashboard\DashboardService;


class DashboardController
{

     public function __construct(
        private DashboardService $dashboardService
    ) {}

    public function index()
    {
        return response()->json(
            $this->dashboardService->getDashboard()
        );
    }

}
