<?php

namespace App\Services;

use App\Actions\PrepareDataToUpdateSpreadsheet;
use App\Models\Row;
use App\Services\RowService;
use App\Models\SpreadSheet;
use App\Models\User;
use App\Services\GoogleSpreadsheetWorkerService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SpreadSheetService
{
    public function __construct(
        protected GoogleSpreadsheetWorkerService $gSsheetWorkerService,
        protected SheetService $sheetService,
        protected RowService $rowService,
        protected SpreadsheetObservationService $sObservationService,
        protected SpreadsheetActionService $sActionService
    ) {}

    /**
     * @return \Illuminate\Database\Eloquent\Collection;
     */
    public function getAll(int $userId): Collection
    {
        return User::find($userId)->spreadsheets;
    }

    /**
     * @param int $spreadsheetId
     * @param int|null $userId To add to observation
     * @return App\Models\SpreadSheet
     */
    public function get(int $spreadsheetId, ?int $userId = null): ?SpreadSheet
    {
        $spreadsheet = SpreadSheet::where('id', $spreadsheetId)->first();

        if (!isset($spreadsheet)) {
            return null;
        }

        if (isset($userId)) {
            $this->sObservationService->addToObservation($spreadsheet->id, $userId);
        }

        return $spreadsheet;
    }

    /**
     * Create Spreadsheet
     * @param array $requestData
     * @param int $userId
     */
    public function createSpreadSheet(array $requestData, int $userId)
    {
        $spreadsheet = $this->gSsheetWorkerService
            ->getSpreadsheet(
                $requestData['url'],
                $requestData['sheet'] ?? null,
            );

        if (!$spreadsheet) {
            return false;
        }

        $spreadSheetExist = SpreadSheet::where(
            'spreadsheet_id',
            $spreadsheet['spreadsheetId']
        )->first();

        if (isset($spreadSheetExist)) {

            if (!$spreadSheetExist->users->find($userId)) {
                DB::table('spread_sheet_user')->insert([
                    'user_id' => $userId,
                    'spread_sheet_id' => $spreadSheetExist->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->updateSpreadSheet(
                $spreadSheetExist->id,
                $requestData['url'],
                $this->sheetService->getByTitle(
                    $spreadSheetExist->id,
                    // $spreadsheet['spreadsheetId'],
                    $spreadsheet['current_sheet']
                )->id,
                $userId,
                $spreadsheet
            );
            return  $spreadSheetExist;
        }
        
        $data = [
            'title' => $spreadsheet['spreadsheetTitle'],
            'spreadsheet_id' => $spreadsheet['spreadsheetId'],
            'url' => $requestData['url'],
            'range' => $spreadsheet['data']['range'] ?? null,
        ];

        $newSpreadSheet = $this->create(
            $data,
            $userId
        );

        DB::table('spread_sheet_user')->insert([
            'user_id' => $userId,
            'spread_sheet_id' => $newSpreadSheet->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $currentSheet = $spreadsheet['current_sheet'];
        $sheetsResult = $this->sheetService->massCreate(
            $spreadsheet['sheets'],
            $newSpreadSheet->id,
            $currentSheet,
            $spreadsheet['data']['range'] ?? null
        );

        if (
            isset($spreadsheet['data']['values'])
            && count($spreadsheet['data']['values']) > 0
        ) {
            $rows = $this->rowService->massCreate(
                $this->sheetService->getCurrent($newSpreadSheet->id)->id,
                $spreadsheet['data']['values'],
                $userId,
            );
        }

        return $newSpreadSheet;
    }

    /**
     * Update Spreadsheet
     *@param int $spreadsheetId
     *@param string $spreadsheetUrl
     *@param string $sheetId
     *@param int $userId
     *@param null|array $spreadsheet = null
     */
    public function updateSpreadSheet(
        int $spreadsheetId,
        string $spreadsheetUrl,
        string $sheetId,
        int $userId,
        ?array $spreadsheet = null
    ) {
        $sheet = $this->sheetService->get($sheetId);

        if (!isset($spreadsheet)) {
            $spreadsheet = $this->gSsheetWorkerService
                ->getSpreadsheet(
                    $spreadsheetUrl,
                    $sheet->title ?? null,
                );
        }
        $data = [
            'title' => $spreadsheet['spreadsheetTitle'],
            'url' => $spreadsheetUrl,
            'range' => $spreadsheet['data']['range'] ?? null,
        ];

        $updatedSpreadsheet = $this->update(
            $spreadsheetId,
            $data,
            $userId
        );

        $currentSheet = $spreadsheet['current_sheet'];
        $sheets = $this->sheetService->massUpdate(
            $spreadsheet['sheets'],
            $spreadsheetId,
            $currentSheet,
            $spreadsheet['data']['range'] ?? null

        );

        if (isset($sheet) && !$sheet->is_initialized) {
            if (
                isset($spreadsheet['data']['values'])
                && count($spreadsheet['data']['values']) > 0
            ) {
                $existedRows = $this->rowService->getAll($sheet->id);

                if (count($existedRows) == 0) {
                    $rows = $this->rowService->massCreate(
                        $this->sheetService->getCurrent(
                            $updatedSpreadsheet->id
                        )->id,
                        $spreadsheet['data']['values'],
                        $userId
                    );
                }

                $sheet->is_initialized = true;
                $sheet->update();
            }
        }

        return $updatedSpreadsheet;
    }

    public function create(array $data, int $userId): SpreadSheet
    {
        $spreadsheet = SpreadSheet::create($data);

        $this->sObservationService->addToObservation($spreadsheet->id, $userId);

        return $spreadsheet;
    }

    public function update(int $id, array $data, int $userId)
    {
        $spreadsheet = SpreadSheet::find($id);
        $spreadsheet->update($data);
        $this->sObservationService->addToObservation($spreadsheet->id, $userId);
        return $spreadsheet;
    }

    public function delete(int $id)
    {
        $spreadsheet = SpreadSheet::find($id);

        if (!isset($spreadsheet->range)) {
            return false;
        }

        $result = $this->gSsheetWorkerService->deleteSpreadsheet(
            $spreadsheet->url,
            $spreadsheet->range
        );

        if (!$result) {
            return false;
        }

        // $result = $this->rowService->deleteAll($id);

        return $result ? true : false;
    }

    // public function synchronizeSpreadsheet(SpreadSheet $spreadsheet)
    // {
    //     // dd('add to actions');
    //     $sheets = $spreadsheet->sheets;
    //     $action = $this->sActionService->createAction();
    //     // $data = PrepareDataToUpdateSpreadsheet::prepare($sheets);

    //     // $result = $this->gSsheetWorkerService->updateSpreadSheet(
    //     //     $spreadsheet->url,
    //     //     $data
    //     // );
    // }
}
