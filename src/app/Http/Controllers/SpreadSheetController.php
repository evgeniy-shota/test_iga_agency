<?php

namespace App\Http\Controllers;

use App\Actions\GetColumnsNames;
use App\Http\Requests\SpreadSheetRequest;
use App\Models\SpreadSheet;
use App\Services\GoogleSpreadsheetWorkerService;
use App\Services\SpreadSheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpreadSheetController extends Controller
{
    public function __construct(
        protected SpreadSheetService $spreadSheetService
    ) {}
    // public function index(Request $request) {}

    public function show(Request $request, int $id)
    {
        return SpreadSheet::where('id', $id)->first();
    }

    public function create(SpreadSheetRequest $request)
    {
        $validated = $request->validated();
        dump($this->spreadSheetService->createSpreadSheet($validated, Auth::id()));
        dd();
    }

    public function update(Request $request, int $id) {}

    public function destroy(Request $request, int $id) {}
}
