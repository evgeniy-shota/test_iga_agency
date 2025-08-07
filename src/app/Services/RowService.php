<?php

namespace App\Services;

use App\Actions\GetColumnsNames;
use App\Actions\GetSpreadsheetRange;
use App\Enums\SpreadsheetActionType;
use App\Services\PrepareGoogleSheetRequestService;
use App\Enums\SpreadSheetRowStatus;
use App\Models\Row;
use App\Models\SpreadSheetAction;
use Database\Factories\SpreadSheetFactory;
use Google\Service\ServiceControl\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;

class RowService
{
    public function __construct(
        protected GoogleSpreadsheetWorkerService $gSWorkerService,
        protected SpreadsheetObservationService $sObservationService,
        protected SheetService $sheetService,
        protected SpreadsheetActionService $sActionService,
        protected PrepareGoogleSheetRequestService $pGoogleSheetRequestService,
    ) {}

    public function getAll(int $sheetId, string $orderBy = 'row_number'): Collection
    {
        return Row::where('sheet_id', $sheetId)->orderBy($orderBy)->orderBy('id')->get();
    }

    /**
     * Return only rows with status 'Allowed' and order by row_number
     * @param int $sheetId sheet id
     */
    public function getAllowed(int $sheetId): Collection
    {
        $rows = Row::where('sheet_id', $sheetId)->allowed()
            ->orderBy('row_number')->get();

        return $rows;
    }

    /**
     * @param int $id Row id
     * @return use App\Models\Row;
     */
    public function get(int $id): Row
    {
        $row = Row::where('id', $id)->first();
        return $row;
    }

    /**
     * Create row
     */
    public function create(int $sheetId, array $data, int $userId)
    {
        $lastNumber = $this->getLastRowNumber($sheetId);
        $rowNum = $lastNumber + 1;

        $newRow = Row::create([
            'sheet_id' => $sheetId,
            'row_number' => $rowNum,
            ...$data
        ]);
        $sheet = $newRow->sheet;
        $spreadsheet = $sheet->spreadsheet;

        if ($newRow->status === SpreadSheetRowStatus::Allowed) {

            if ($sheet->row_count <= $rowNum) {
                $requestData = $this->pGoogleSheetRequestService->prepareAppendRows(
                    $sheet->sheet_id,
                    1,
                );
                $result = $this->sActionService->createAction(
                    $spreadsheet->id,
                    SpreadsheetActionType::Insert,
                    $requestData,
                );
            }
        }

        $this->sObservationService->addToObservation(
            $spreadsheet->id,
            $userId
        );

        return $newRow;
    }

    /**
     * @param int $sheetId
     * @return int 
     */
    public function getLastRowNumber(int $sheetId): int
    {
        $rowNumber = Row::where('sheet_id', $sheetId)->max('row_number');
        return $rowNumber ?? 0;
    }

    /**
     * @param int $sheetId
     * @param array $data
     * @param int $userId
     */
    public function massCreate(int $sheetId, array $data, int $userId)
    {
        $lastRowNumber = $this->getLastRowNumber($sheetId);

        for ($i = 0, $size = count($data); $i < $size; $i++) {
            $rows[$i]['sheet_id'] = $sheetId;
            $rows[$i]['row_number'] = $data[$i]['row_number'] ?? ++$lastRowNumber;
            $rows[$i]['status'] = $data[$i]['status'] ?? SpreadSheetRowStatus::Allowed;
            $rows[$i]['name'] = $data[$i]['name'] ?? null;
            $rows[$i]['reserved_count'] = $data[$i]['reserved_count'] ?? null;
            $rows[$i]['total_count'] = $data[$i]['total_count'] ?? null;
        }

        $newRows = Row::insert($rows);
        $this->sObservationService->addToObservation(
            $this->sheetService->get($sheetId)->spreadsheet->id,
            $userId
        );

        return $newRows;
    }

    /**
     * Add 100 rows
     */
    public function addMultipleRows(int $sheetId, int $number, int $userId)
    {
        $sheet = $this->sheetService->get($sheetId);
        $spreadsheet = $sheet->spreadsheet;
        $existedRows = $this->getAll($sheetId);

        if ($sheet->row_count - count($existedRows) <= $number) {
            $requestData = $this->pGoogleSheetRequestService->prepareAppendRows(
                $sheet->sheet_id,
                $number
            );
            $result = $this->sActionService->createAction(
                $spreadsheet->id,
                SpreadsheetActionType::AppendRows,
                $requestData,
            );
        }

        $indx = $number / 2;
        $data_allowed = array_fill(
            0,
            $indx,
            ['status' => SpreadSheetRowStatus::Allowed->value]
        );
        $data_prohibited = array_fill(
            $indx,
            $indx,
            ['status' => SpreadSheetRowStatus::Prohibited->value]
        );
        $rows = $this->massCreate(
            $sheetId,
            array_merge($data_allowed, $data_prohibited),
            $userId
        );

        return $rows;
    }

    /**
     * Update row
     */
    public function update(int $id, array $data, $userId)
    {
        $row = Row::find($id);
        $oldStatus = $row->status->value;
        $sheet = $row->sheet;
        $spreadsheet = $sheet->spreadsheet;
        $row->update($data);
        $row->save();

        if ($oldStatus !== $data['status']) {
            if ($data['status'] === SpreadSheetRowStatus::Allowed->value) {
                $insertRowNum = $this->findIndxInAllowed($row->id, $sheet->id)
                    ?? $row->row_number;
                $requestData = $this->pGoogleSheetRequestService
                    ->prepareInsertRows(
                        $sheet->sheet_id,
                        $insertRowNum,
                        $insertRowNum,
                    );
                $actionType = SpreadsheetActionType::Insert;
            } else {
                $deleteRowNum = $this->findPotentialIndxInAllowed(
                    $row->row_number,
                    $sheet->id
                );
                $requestData = $this->pGoogleSheetRequestService
                    ->prepareDeleteRows(
                        $sheet->sheet_id,
                        $deleteRowNum,
                        $deleteRowNum,
                    );
                $actionType = SpreadsheetActionType::Delete;
            }
            /**
             * Add or remove a row depending on the status
             */
            $result = $this->sActionService->createAction(
                $spreadsheet->id,
                $actionType,
                $requestData,
            );
        }

        $this->sObservationService->addToObservation(
            $spreadsheet->id,
            $userId
        );

        return $row;
    }

    /**
     * Decrease or increase row_number for all rows whose row_number is greater than startRowNumber
     * @param int $sheetId sheet_id from sheets db
     * @param int $startRowNumber 
     * @param bool $decrementRowNumber false - increment
     * @param int|null $excludeRowId 
     */
    public function changeRowNumber(
        int $sheetId,
        int $startRowNumber,
        bool $decrementRowNumber = true
    ) {
        $updateResult = Row::where('sheet_id', $sheetId)
            ->where('row_number', '>', $startRowNumber)
            ->tap(function ($query) use ($decrementRowNumber) {
                $query->when(
                    $decrementRowNumber,
                    function (Builder $query) {
                        $query->decrement('row_number');
                    },
                    function (Builder $query) {
                        $query->increment('row_number');
                    }
                );
            })->get();

        return $updateResult;
    }

    /**
     * Delete row
     */
    public function delete(int $id, int $userId)
    {
        $row = $this->get($id);
        $sheet = $row->sheet;
        $spreadsheet = $sheet->spreadsheet;
        $rowNumber = $row->row_number;
        $rowStatus = $row->status;
        $result = $row->delete($id);
        $decrRowNumRows = $this->changeRowNumber($sheet->id, $rowNumber);

        if ($rowStatus->value !== SpreadSheetRowStatus::Prohibited->value) {
            $requestData = $this->pGoogleSheetRequestService
                ->prepareDeleteRows(
                    $sheet->sheet_id,
                    $rowNumber,
                    $rowNumber
                );
            $result = $this->sActionService->createAction(
                $spreadsheet->id,
                SpreadsheetActionType::Delete,
                $requestData,
            );
        }

        $this->sObservationService->addToObservation(
            $spreadsheet->id,
            $userId
        );

        return $result;
    }

    /**
     * Delete all rows on sheet. 
     * 1.Clear all
     * 2.Delete all
     * 3.Append rows
     * @param int $sheetId
     * @param int $userId 
     */
    public function deleteAllRows(int $sheetId, int $userId)
    {
        $sheet = $this->sheetService->get($sheetId);
        // dd('clear sheet: ' . $sheet->sheet_id);

        $spreadsheet = $sheet->spreadsheet;

        $requestData = $this->pGoogleSheetRequestService
            ->prepareClearSheet(
                $sheet->sheet_id
            );
        $result = $this->sActionService->createAction(
            $spreadsheet->id,
            SpreadsheetActionType::Clear,
            $requestData,
        );

        $requestData = $this->pGoogleSheetRequestService
            ->prepareDeleteRows(
                $sheet->sheet_id,
                2
            );
        $result = $this->sActionService->createAction(
            $spreadsheet->id,
            SpreadsheetActionType::Delete,
            $requestData,
        );

        $requestData = $this->pGoogleSheetRequestService
            ->prepareAppendRows(
                $sheet->sheet_id,
                999
            );
        $result = $this->sActionService->createAction(
            $spreadsheet->id,
            SpreadsheetActionType::AppendRows,
            $requestData,
        );

        $this->sObservationService->addToObservation(
            $spreadsheet->id,
            $userId
        );

        return Row::where('sheet_id', $sheetId)->delete();
    }

    /**
     * Search row index in allowed rows array for correct insert rows
     * @param int $rowId
     * @param int $sheetId
     * @return int|false
     */
    private function findIndxInAllowed(int $rowId, int $sheetId): int|bool
    {
        $allowedRows = $this->getAllowed($sheetId);

        foreach ($allowedRows as $key => $aRow) {
            if ($aRow->id == $rowId) {
                return  $key + 1;
            }
        }

        return false;
    }

    /**
     * Search potential row index in allowed rows array for correct remove row
     * @param int $rowNumber
     * @param int $sheetId
     * @return int
     */
    private function findPotentialIndxInAllowed(
        int $rowNumber,
        int $sheetId
    ): int {
        $allowedRows = $this->getAllowed($sheetId);

        for ($i = 0, $size = count($allowedRows); $i < $size; $i++) {
            if ($allowedRows[$i]->row_number > $rowNumber) {
                return $i + 1;
            }
        }

        return 1;
    }
}
