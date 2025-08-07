<?php

namespace App\Http\Controllers;

use App\Models\Row;
use App\Models\SpreadSheet;
use App\Models\SpreadSheetData;
use App\Services\DashboardService;
use App\Services\RowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        protected RowService $rowService,
        protected DashboardService $dashboardService
    ) {}

    public function index()
    {
        $spreadsheet = $this->dashboardService->getLatestUserSpreadsheet(Auth::id());

        if ($spreadsheet) {
            return to_route('dashboard.show', $spreadsheet->id);
        }

        return Inertia::render(
            'Dashboard',
            ['sheets' => []],
        );
    }

    public function show(Request $request, string $id)
    {
        $data = $this->dashboardService->getDashboardData($id, Auth::id());

        if (!$data) {
            return to_route('dashboard');
        }

        return Inertia::render(
            'Dashboard',
            $data
        );
    }
}
