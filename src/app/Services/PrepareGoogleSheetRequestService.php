<?php

namespace App\Services;

use App\Actions\PrepareDataToUpdateSpreadsheet;
use App\Actions\PrepareRowsDataToUpdate;
use App\Models\Row;
use App\Models\Sheet;
use Illuminate\Database\Eloquent\Collection;

class PrepareGoogleSheetRequestService
{
    /**
     * Request data for adding rows to a range from startRowIndex to endRowIndex
     * @param int $sheetId sheet_id from sheets table
     * @param int $startRowNum
     * @param null|int $endRowNum
     * @param int $startColumnIndex
     * @param null|int $endColumnIndex
     * @return array
     */
    public function prepareInsertRows(
        int $sheetId,
        int $startRowNum,
        null|int $endRowNum = null,
        int $startColumnIndex = 0,
        null|int $endColumnIndex = null,
    ): array {
        $range = [
            "sheetId" => $sheetId,
            "startRowIndex" => $startRowNum - 1,
            "startColumnIndex" => $startColumnIndex,
        ];

        if (isset($endRowNum)) {
            $range = array_merge($range, ["endRowIndex" => $endRowNum]);
        }

        if (isset($endColumnIndex)) {
            $range = array_merge($range, ["endColumnIndex" => $endColumnIndex]);
        }

        $requests = [
            'insertRange' => [
                'range' => $range,
                'shiftDimension' => 'ROWS',
            ]
        ];

        return $requests;
    }

    /**
     * Request data for adding rows to the end of a sheet
     * @param int $sheetId sheet_id from Sheet model
     * @param int $number 
     * @return array 
     */
    public function prepareAppendRows(int $sheetId, int $number = 1000): array
    {
        $requests = [
            'appendDimension' => [
                "sheetId" => $sheetId,
                "dimension" => 'ROWS',
                "length" => $number
            ]
        ];

        return $requests;
    }

    /**
     * Request data for update row on sheet
     * @param int $sheetId sheet_id from sheets db
     */
    // public function prepareUpdateRow(Row $row, int $sheetId)
    // {
    //     // $collection = collect($row);
    //     $requests = PrepareRowsDataToUpdate::prepare([$row], $sheetId);

    //     return $requests;
    // }

    /**
     * Request data for update rows on sheet
     * @param int $sheetId sheet_id from sheets db
     */
    // public function prepareUpdateRows(array $rows, int $sheetId)
    // {
    //     $requests = PrepareRowsDataToUpdate::prepare($rows, $sheetId);

    //     return $requests;
    // }

    /**
     * Request data for update sheets
     */
    public function prepareUpdateSheets(Collection $sheets)
    {
        $requests = PrepareDataToUpdateSpreadsheet::prepare3($sheets);

        return $requests;
    }

    /**
     * @param int $sheetId sheet_id from Sheet model
     * @return array 
     */
    public function prepareClearSheet(int $sheetId): array
    {
        $requests = [
            'updateCells' => [
                'rows' => [],
                'range' => [
                    'sheetId' => $sheetId,
                    'startRowIndex' => 0,
                    // 'endRowIndex' => '',
                ],
                'fields' => '*',
            ]
        ];

        return $requests;
    }

    /**
     * Request data for delete rows on sheet
     */
    public function prepareDeleteRows(
        int $sheetId,
        int $startRowNum,
        null|int $endRowNum = null,
        bool $appendRows = true
    ): array {

        $range = [
            "sheetId" => $sheetId,
            'dimension' => 'ROWS',
            "startIndex" => $startRowNum - 1,
        ];

        if (isset($endRowNum)) {
            $range = array_merge($range, ["endIndex" => $endRowNum]);
        }

        $requests = [
            'deleteDimension' => [
                'range' => $range,
            ]
        ];

        return $requests;
    }
}
