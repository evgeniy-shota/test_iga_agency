<?php

namespace App\Services;

use App\Actions\SynchronizeSpreadsheet;
use App\Jobs\UpdateSpreadSheet;
use App\Models\SpreadsheetUnderObservation;
use DateTime;

class SpreadsheetObservationService
{
    public function addToObservation(int $spreadsheetId, int $userId)
    {
        $spreadsheet = SpreadsheetUnderObservation::where('spread_sheet_id', $spreadsheetId)->first();
        $date = now()->format('Y-m-d H:i:s');

        $data = [
            'spread_sheet_id' => $spreadsheetId,
            'last_access' => $date,
            'user_id' => $userId
        ];

        if (isset($spreadsheet)) {
            $sUnderObservaation = $spreadsheet->update($data);
            $spreadsheet->save();
        } else {
            $sUnderObservaation = SpreadsheetUnderObservation::create($data);
        }

        return $sUnderObservaation;
    }

    public function removeFromObservation(int $spreadsheetId, int $userId)
    {
        return SpreadsheetUnderObservation::where(
            'spread_sheet_id',
            $spreadsheetId
        )->delete();
    }

    public function getSpreadsheetsUnderObservation()
    {
        $spreadsheets = SpreadsheetUnderObservation::where(
            'last_access',
            '>=',
            now()->subSeconds(55)
        )->get();

        return $spreadsheets;
    }

    public function observeSpreadsheets()
    {
        $spreadsheetsForObserve = $this->getSpreadsheetsUnderObservation();

        foreach ($spreadsheetsForObserve as $spreadsheetForObserve) {
            UpdateSpreadSheet::dispatch(
                $spreadsheetForObserve->spreadsheet,
            );
        }
    }
}
