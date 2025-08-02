<?php

namespace App\Services;

use App\Enums\SpreadSheetLineStatus;
use App\Models\SpreadSheetData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RowService
{
    public function getAll(?int $userId, string $orderBy = 'id')
    {
        return SpreadSheetData::when(
            isset($userId),
            function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            }
        )->orderBy($orderBy)->get();
    }

    public function get(int $id)
    {
        return SpreadSheetData::where('id', $id)->first();
    }

    public function create(int $spreadsheetId, array $data, int $userId)
    {
        $dataToAdd = array_map(function ($row) use ($spreadsheetId, $userId) {
            $res['columns'] = json_encode($row, JSON_UNESCAPED_UNICODE);
            $res['spread_sheets_id'] = $spreadsheetId;
            $res['status'] = SpreadSheetLineStatus::Allowed;
            $res['user_id'] = $userId;

            return $res;
        }, $data);

        DB::table('rows')->where('user_id', $userId)->delete();

        DB::table('rows')->insert($dataToAdd);

        return true;
    }

    public function update(int $id, array $data, int $userId) {}

    public function delete(int $id, int $userId) {}

    public function deleteAll(int $spreadsheetId) {}

    public function massCreate(int $spreadsheetId, int $userId, int $count = 1000)
    {
        $columns = SpreadSheetData::where('user_id', $userId)
            ->select('columns')->first();

        $decodedColumns = json_decode($columns, true);
        $columnsRes = array_combine(
            array_keys($decodedColumns),
            array_fill(0, count($decodedColumns), '')
        );

        for ($i = 0, $size = $count; $i < $size; $i++) {
            $data[$i] = [
                'spread_sheets_id' => $spreadsheetId,
                'status' => $i % 2 == 0
                    ? SpreadSheetLineStatus::Allowed->value
                    : SpreadSheetLineStatus::Allowed->value,
                'columns' => $columnsRes,
            ];
        }

        $result = DB::table('spread_sheet_data')->insert([$data]);

        return $result;
    }
}
