<?php

namespace App\Http\Controllers;

use App\Actions\GetColumnsNames;
use App\Http\Requests\SpreadSheet\CreateRequest;
use App\Http\Requests\SpreadSheet\UpdateRequest;
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

    public function create(CreateRequest $request)
    {
        $validated = $request->validated();
        $spreadsheet = $this->spreadSheetService->createSpreadSheet($validated, Auth::id());

        if (!$spreadsheet) {
            return redirect()->back()
                ->with(
                    'error',
                    'Something went wrong...The table may not be available.'
                );
        }

        return redirect(route('dashboard.show', $spreadsheet->id));
    }

    public function update(UpdateRequest $request, int $id)
    {
        $validated = $request->validated();
        $spreadsheet = $this->spreadSheetService->updateSpreadSheet($id, $validated['url'], $validated['sheet'],  Auth::id());

        return redirect(route('dashboard.show', $spreadsheet->id));
    }

    public function destroy(Request $request, int $id)
    {
        $this->spreadSheetService->delete($id);
        return redirect(route('dashboard'));
    }
}
