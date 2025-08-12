<?php

namespace App\Services;

use App\Actions\ParseGoogleSpreadSheetUrl;
use App\Actions\ReadCredentials;

class GoogleSpreadsheetWorkerService
{
    private function prepareSheetService(string $spreadsheetUrl): false|GoogleSheetsService
    {
        $credentials = json_decode(ReadCredentials::read(), true);
        $apiClient = new GoogleApiClientService($credentials);
        $sheetsService = new GoogleSheetsService($apiClient->client);

        $parsedUrl = ParseGoogleSpreadSheetUrl::parse($spreadsheetUrl);

        $isSpreadsheetReceived = $sheetsService->getSpreadsheet(
            $parsedUrl['spreadsheetId']
        );

        if (!$isSpreadsheetReceived) {
            return false;
        }

        return $sheetsService;
    }

    public function getSpreadsheet(
        string $spreadsheetUrl,
        ?string $sheetTitle = null,
        bool $parseValue = true
    ) {
        $parsedUrl = ParseGoogleSpreadSheetUrl::parse($spreadsheetUrl);
        $sheetsService = $this->prepareSheetService($spreadsheetUrl);

        if (!$sheetsService) {
            return false;
        }

        $sheetsService->getSheets();

        if (
            !isset($sheetTitle)
            || !$sheetsService->checkSheetTitleExists($sheetTitle)
        ) {
            $sheetTitle = $sheetsService
                ->getSheetTitleBySheetId($parsedUrl['sheetId']);
        }

        $sheetsService->getSpreadsheetValues(
            $sheetTitle
        );

        return [
            'spreadsheetTitle' => $sheetsService->spreadsheet()['properties']['title'],
            'spreadsheetId' => $parsedUrl['spreadsheetId'],
            'sheets' => $sheetsService->sheets(),
            'current_sheet' => $sheetTitle,
            'data' => $sheetsService->spreadsheetValues($parseValue),
        ];
    }

    public function updateSpreadsheet2(string $spreadsheetUrl, array $data)
    {
        $sheetsService = $this->prepareSheetService($spreadsheetUrl);
        $response = $sheetsService->batchUpdateRequest($data);
        return $response;
    }

}
