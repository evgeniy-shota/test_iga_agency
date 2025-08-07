<?php

namespace App\Actions;

use App\Models\Row;
use App\Models\SpreadSheet;
use Illuminate\Database\Eloquent\Collection;

class PrepareDataToUpdateSpreadsheet
{

    /** Prepares data for sending
     * @param \Illuminate\Database\Eloquent\Collection<Sheet> $sheets
     */
    public static function prepare3(Collection $sheets): array
    {
        $result = [];
        // $reultCounter = 0;

        foreach ($sheets as $sheet) {
            $data = Row::where('sheet_id', $sheet->id)->allowed()
                ->orderBy('row_number')->get()->toArray();

            if (count($data) == 0) {
                continue;
            }

            $updateCellData = [
                'rows' => [],
                'range' => [],
                'fields' => '*',
            ];

            $rows = [];
            $values = [];

            for ($i = 0, $size = count($data); $i < $size; $i++) {

                $cellValues = [
                    ["userEnteredValue" => ["numberValue" => $data[$i]['id']]],
                    ["userEnteredValue" => ["numberValue" => $data[$i]['sheet_id']]],
                    ["userEnteredValue" => ["numberValue" => $data[$i]['row_number']]],
                    ["userEnteredValue" => ["stringValue" => $data[$i]['status']]],
                    ["userEnteredValue" => ["stringValue" => $data[$i]['name']]],
                    ["userEnteredValue" => ["numberValue" => $data[$i]['reserved_count']]],
                    ["userEnteredValue" => ["numberValue" => $data[$i]['total_count']]],
                ];

                $values[] = ['values' => $cellValues];
            }

            $cellRange = [
                "sheetId" => $sheet->sheet_id,
                "startRowIndex" => 0,
                "startColumnIndex" => 0,
                "endColumnIndex" => count($cellValues),
            ];

            $updateCellData['rows'] = $values;
            $updateCellData['range'] = $cellRange;

            $result[] = ['updateCells' => [
                ...$updateCellData,
                'fields' => '*',
            ]];
        }

        return $result;
    }
}
