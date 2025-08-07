<?php

namespace App\Actions;

use App\Models\Row;
use App\Models\SpreadSheet;
use Illuminate\Support\Collection;

// use Illuminate\Database\Eloquent\Collection;

class PrepareRowsDataToUpdate
{
    /** Prepares data for sending
     * @param array $sheets
     * @param int $sheetId sheet_id from sheets db
     */
    public static function prepare(array $rows, int $sheetId): array
    {
        $result = [];

        for ($i = 0, $size = count($rows); $i < $size; $i++) {
            $updateCellData = [
                'rows' => [],
                'range' => [],
                'fields' => '*',
            ];

            $cellValues = [
                ["userEnteredValue" => ["numberValue" => $rows[$i]['id']]],
                ["userEnteredValue" => ["numberValue" => $rows[$i]['sheet_id']]],
                ["userEnteredValue" => ["numberValue" => $rows[$i]['row_number']]],
                ["userEnteredValue" => ["stringValue" => $rows[$i]['status']]],
                ["userEnteredValue" => ["stringValue" => $rows[$i]['name']]],
                ["userEnteredValue" => ["numberValue" => $rows[$i]['reserved_count']]],
                ["userEnteredValue" => ["numberValue" => $rows[$i]['total_count']]],
            ];

            $cellRange = [
                "sheetId" => $sheetId,
                "startRowIndex" => $rows[$i]['row_number'] - 1,
                "endRowIndex" => $rows[$i]['row_number'],
                "startColumnIndex" => 0,
                "endColumnIndex" => count($rows),
            ];

            $updateCellData['rows'][] = ['values' => $cellValues];
            $updateCellData['range'] = $cellRange;

            $result[] = ['updateCells' => [
                ...$updateCellData,
                'fields' => '*',
            ]];
        }

        return $result;
    }
}
