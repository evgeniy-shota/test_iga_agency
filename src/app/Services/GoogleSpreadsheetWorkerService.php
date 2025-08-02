<?php

namespace App\Services;

use App\Actions\ParseGoogleSpreadSheetUrl;
use App\Actions\ReadCredentials;

class GoogleSpreadsheetWorkerService
{
    private function prepareSheetService(): GoogleSheetsService
    {
        $credentials = json_decode(ReadCredentials::read(), true);
        $apiClient = new GoogleApiClientService($credentials);
        $sheetsService = new GoogleSheetsService($apiClient->client);

        return $sheetsService;
    }

    public function getSpreadsheet(string $spreadsheetUrl, ?string $sheetTitle)
    {
        $parsedUrl = ParseGoogleSpreadSheetUrl::parse($spreadsheetUrl);
        $spreadsheetId = $parsedUrl['spreadsheetId'];
        $sheetId = $parsedUrl['sheetId'];


        $sheetsService = $this->prepareSheetService();

        $isSpreadsheetReceived = $sheetsService->getSpreadsheet($spreadsheetId);

        if (!$isSpreadsheetReceived) {
            return false;
        }

        $sheetsService->getSheets();

        if (
            !isset($sheetTitle)
            || !$sheetsService->checkSheetTitleExists($sheetTitle)
        ) {
            $sheetTitle = $sheetsService
                ->getSheetTitleBySheetId($sheetId);
        }

        $sheetsService->getSpreadsheetValues(
            $spreadsheetId,
            $sheetTitle
        );

        return [
            'sheets' => $sheetsService->sheets(),
            'current_sheet' => $sheetTitle,
            'data' => $sheetsService->spreadsheetValues(),
        ];
    }

    public function updateSpreadsheet($spreadsheetUrl) {}

    public function deleteSpreadsheet($spreadsheetUrl) {}
}
