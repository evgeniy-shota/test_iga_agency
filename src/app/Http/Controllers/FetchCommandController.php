<?php

namespace App\Http\Controllers;

use App\Services\SpreadSheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FetchCommandController extends Controller
{
    public function __construct(
        protected SpreadSheetService $spreadSheetService
    ) {}

    public function getSpreadsheetCommand(Request $request, ?int $count = null)
    {
        $spreadsheets = $this->spreadSheetService->getAll(Auth::id());

        Artisan::call('app:get-google-spreadsheet', ['count' => $count]);
        $output = Artisan::output();

        return Inertia::render('FetchOutput', [
            'spreadsheets' => $spreadsheets,
            'output' => $output,
            'message' => 'Row created',
        ]);
    }
}
