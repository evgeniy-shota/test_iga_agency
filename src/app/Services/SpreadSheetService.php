<?php

namespace App\Services;

use App\Services\RowService;
use App\Models\SpreadSheet;
use App\Services\GoogleSpreadsheetWorkerService;
use Illuminate\Database\Eloquent\Builder;

class SpreadSheetService
{
    public function __construct(
        protected GoogleSpreadsheetWorkerService $gSsheetWorkerService,
        protected RowService $rowService
    ) {}

    public function getAll(?int $userId, string $orderBy = 'id')
    {
        return SpreadSheet::when(
            isset($userId),
            function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            }
        )->orderBy($orderBy)->get();
    }

    public function get(int $id)
    {
        return SpreadSheet::where('id', $id)->first();
    }

    public function createSpreadSheet(array $requestData, int $userId)
    {
        $spreadsheetData = $this->gSsheetWorkerService
            ->getSpreadsheet(
                $requestData['url'],
                $requestData['sheetTitle'] ?? 'list 3'
            );

        $data = [
            'url' => $requestData['url'],
            'sheets' => json_encode($spreadsheetData['sheets'], JSON_UNESCAPED_UNICODE),
            'current_sheet' => $spreadsheetData['current_sheet'],
            'range' => $spreadsheetData['data']['range'],
        ];

        $newSpreadSheet = $this->create(
            $data,
            $userId
        );

        $rows = $this->rowService->create(
            $newSpreadSheet->id,
            $spreadsheetData['data']['values'],
            $userId,
        );

        return $newSpreadSheet;
    }

    public function create(array $data, int $userId)
    {
        $spreadsheet = SpreadSheet::updateOrCreate([
            'user_id' => $userId,
        ], [
            ...$data
        ]);

        return $spreadsheet;
    }

    public function update(int $id, array $data)
    {
        $spreadsheet = SpreadSheet::find($id);
        $spreadsheet->update($data);
        return $spreadsheet;
    }

    public function delete(int $id)
    {
        $result = SpreadSheet::delete($id);
        return $result;
    }
}
