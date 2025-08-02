<?php

namespace App\Http\Controllers;

use App\Models\Row;
use App\Models\SpreadSheet;
use App\Models\SpreadSheetData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $spreadsheet = SpreadSheet::where('user_id', Auth::id())->first();
        $spreadsheetData = Row::where('spread_sheets_id', $spreadsheet->id)
            ->orderBy('id')->get()->toArray();

        for ($i = 0, $size = count($spreadsheetData); $i < $size; $i++) {
            $spreadsheetData[$i] = array_merge(
                $spreadsheetData[$i],
                json_decode($spreadsheetData[$i]['columns'], true)
            );
            unset($spreadsheetData[$i]['columns']);
        }

        $columns = array_keys($spreadsheetData[0]);

        return Inertia::render(
            'Dashboard',
            [
                'spreadsheet' => $spreadsheet,
                'columns' => $columns,
                'spreadsheetData' => $spreadsheetData,
            ]
        );
    }

    public function show() {}
}
