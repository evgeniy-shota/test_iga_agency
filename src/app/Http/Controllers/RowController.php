<?php

namespace App\Http\Controllers;

use App\Actions\GetColumnsNames;
use App\Enums\SpreadSheetRowStatus;
use App\Http\Requests\Row\CreateRequest;
use App\Http\Requests\Row\UpdateRequest;
use App\Services\GoogleSpreadsheetWorkerService;
use App\Services\RowService;
use App\Services\SheetService;
use App\Services\SpreadSheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RowController extends Controller
{
    public function __construct(
        protected RowService $rowService,
        protected SheetService $sheetService,
        protected SpreadSheetService $spreadSheetService,
        protected GoogleSpreadsheetWorkerService $gSpreadsheetWorkerService
    ) {}

    public function index(Request $request)
    {
        $sheetId = $request->get('sheetId');
        $spreadsheets = $this->spreadSheetService->getAll(Auth::id());

        return Inertia::render('RowEditor', [
            'spreadsheets' => $spreadsheets,
            'row' => null,
            'availableRowStatuses' => SpreadSheetRowStatus::getValues(),
            'sheetId' => $sheetId,
        ]);
    }

    public function show(Request $request, int $id)
    {
        $row = $this->rowService->get($id);
        $spreadsheets = $this->spreadSheetService->getAll(Auth::id());
        return Inertia::render('RowEditor', [
            'spreadsheets' => $spreadsheets,
            'row' => $row,
            'availableRowStatuses' => SpreadSheetRowStatus::getValues(),
            'sheetId' => $row->sheet->id
        ]);
    }

    /**
     * @param int $id
     */
    public function addMultipleRows(Request $request, int $sheetId)
    {
        $rowsNumber = $request->get('number');
        $result = $this->rowService->addMultipleRows($sheetId, $rowsNumber, Auth::id());

        if (!$result) {
            return redirect()->back()->with('message', 'Something gone wrong...Rows not added');
        }

        return to_route('dashboard.show', $sheetId);
        return to_route('dashboard.show', $sheet->spreadsheet->id);
    }

    public function create(CreateRequest $request)
    {
        $validated = $request->validated();
        $newRow = $this->rowService->create($validated['sheet_id'], $validated, Auth::id());

        if (!isset($newRow)) {
            return redirect()->back()->with(['message' => 'Something gone wrong, row not created']);
        }

        $spreadsheets = $this->spreadSheetService->getAll(Auth::id());

        return to_route('dashboard.show', $newRow->sheet_id)->with('message', 'Row created');
    }

    public function update(UpdateRequest $request, int $id)
    {
        $validated = $request->validated();
        $updatedRow = $this->rowService->update($id, $validated, Auth::id());

        if (!$updatedRow) {
            return redirect()->back()->with(['message' => 'Something gone wrong, row not updated']);
        }

        return to_route('dashboard.show', $updatedRow->sheet_id)->with('message', 'Row created');

        $spreadsheets = $this->spreadSheetService->getAll(Auth::id());

        return Inertia::render('RowEditor', [
            'spreadsheets' => $spreadsheets,
            'row' => $updatedRow,
            'availableRowStatuses' => SpreadSheetRowStatus::getValues(),
            'message' => 'Row updated',
            'sheetId' => $updatedRow->sheet_id,
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $sheet = $this->rowService->get($id)->sheet;
        $result = $this->rowService->delete($id, Auth::id());

        if (!$result) {
            return back()->withErrors(['message' => 'Something gone wrong, row not deleted']);
        }

        return to_route('dashboard.show', $sheet->id);
    }

    public function deleteAllRows(int $sheetId)
    {
        $this->rowService->deleteAllRows($sheetId, Auth::id());

        return to_route('dashboard.show', $sheetId);
    }
}
