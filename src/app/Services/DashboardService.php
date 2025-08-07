<?php

namespace App\Services;

use App\Models\SpreadsheetUnderObservation;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function __construct(
        protected SpreadsheetObservationService $sObservationService,
        protected SpreadSheetService $spreadSheetService,
        protected SheetService $sheetService,
        protected RowService $rowService
    ) {}

    public function getLatestUserSpreadsheet(int $userId)
    {
        $spreadsheets = SpreadsheetUnderObservation::where('user_id', $userId)
            ->orderByAccess(false)->get();

        if (count($spreadsheets) > 0) {
            $spreadsheet = $spreadsheets[0]->spreadsheet;
            return $spreadsheet;
        }

        return null;
    }

    public function getDashboardData(int $id, int $userId): array|false
    {
        $spreadsheets = $this->spreadSheetService->getAll(Auth::id());
        $spreadsheet = $spreadsheets->find($id);

        if (!isset($spreadsheet)) {
            return false;
        }

        $this->sObservationService->addToObservation($spreadsheet->id, $userId);
        $sheets = $this->sheetService->getAll($spreadsheet->id);
        $currentSheet = $sheets->where('is_current', true)->first();

        $rows = $this->rowService->getAll(
            $currentSheet->id
        )->toArray();

        return [
            'spreadsheets' => $spreadsheets,
            // 'spreadsheet' => $updatedSpreadsheet,
            'spreadsheet' => $spreadsheet,
            'sheets' => $sheets,
            'columns' => count($rows) > 0 ? array_keys($rows[0]) : [],
            'rows' => $rows,
        ];
    }
}
