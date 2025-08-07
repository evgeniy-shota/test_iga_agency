<?php

namespace App\Actions;

use App\Models\SpreadSheet;
use App\Services\GoogleSpreadsheetWorkerService;
use App\Services\PrepareGoogleSheetRequestService;
use App\Services\SpreadsheetActionService;

class SynchronizeSpreadsheet
{
    public function __construct(
        protected GoogleSpreadsheetWorkerService $gSsheetWorkerService,
        protected SpreadsheetActionService $sActionService,
        protected PrepareGoogleSheetRequestService $prepareGoogleSheetRequestService
    ) {}
    public function synchronize(
        SpreadSheet $spreadsheet
    ) {
        $actions = $this->sActionService->getAllObservationActions(
            $spreadsheet->id
        );

        $actionsRequest = array_map(function ($action) {
            return json_decode($action['action_data'], true);
        }, $actions->toArray());

        $sheets = $spreadsheet->sheets;
        $data = $this->prepareGoogleSheetRequestService->prepareUpdateSheets(
            $sheets
        );
        array_push($actionsRequest, ...$data);
        // dd(json_encode($actions));
        if (count($actionsRequest) == 0) {
            dump('empty');
            return true;
        }
        dump('~~~~~~ update spreadsheet ~~~~~~~');
        $result = $this->gSsheetWorkerService->updateSpreadsheet2(
            $spreadsheet->url,
            $actionsRequest
        );

        if ($result) {
            $this->sActionService->changeStatus(
                $actions->select('id')->toArray()
            );
        }

        return $result;
    }
}
