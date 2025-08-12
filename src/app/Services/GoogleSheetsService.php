<?php

namespace App\Services;

use App\Actions\AddMissingValuesToRow;
use App\Actions\GetColumnsNames;
use App\Actions\ParseGoogleSpreadSheet;
use Error;
use Exception;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\AppendDimensionRequest;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\ClearValuesRequest;
use Google\Service\Sheets\ClearValuesResponse;
use Google\Service\Sheets\Request;
use Google\Service\Sheets\Sheet;
use Google\Service\Sheets\Spreadsheet;
use Google\Service\Sheets\ValueRange;
use Psy\Exception\ThrowUpException;

use function Illuminate\Log\log;

class GoogleSheetsService
{
    protected Sheets $service;
    protected Spreadsheet $spreadsheet;
    protected ValueRange $spreadsheetValues;
    /** @var array<\Google\Service\Sheets\Sheet>  */
    protected array $sheets;

    public function __construct(
        protected Client $client
    ) {
        $this->service = new Sheets($client);
    }

    /**
     * Get Google spreadsheet
     * @param string $spreadsheetId SpreadsheetId is a spreadsheet id from Google spreadsheet url
     */
    public function getSpreadsheet(string $spreadsheetId)
    {
        try {
            $this->spreadsheet = $this->service->spreadsheets->get($spreadsheetId);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Get Google spreadsheet values from range
     * @param string $spreadsheetId SpreadsheetId is a spreadsheet id from Google spreadsheet url
     */
    public function getSpreadsheetValues(string $range)
    {
        // $this->spreadsheetValues = $this->service->spreadsheets_values->get($spreadsheetId, $range);
        $this->spreadsheetValues = $this->service->spreadsheets_values->get(
            $this->spreadsheet['spreadsheetId'],
            $range
        );
    }

    /**
     * Get Sheets from spreadsheet
     */
    public function getSheets()
    {
        if (!isset($this->spreadsheet)) {
            throw new Exception('Spreadsheet is not seted');
        }

        $this->sheets = $this->spreadsheet->getSheets();
    }

    /**
     * @return \Google\Service\Sheets\Spreadsheet
     */
    public function spreadsheet(): ?Spreadsheet
    {
        return $this->spreadsheet ?? null;
    }

    public function spreadsheetValues(bool $parse = true)
    {
        if (
            !isset($this->spreadsheetValues)
            || count($this->spreadsheetValues) == 0
        ) {
            return null;
        }

        $range = $this->spreadsheetValues->range;

        $values = $parse
            ? ParseGoogleSpreadSheet::parse($this->spreadsheetValues->values)
            : $this->spreadsheetValues->values;

        return ([
            'range' => $range,
            'values' => $values,
        ]);
    }

    /**
     * @return array<\Google\Service\Sheets\Sheet>
     */
    public function sheets(): ?array
    {
        if (!isset($this->sheets)) {
            return null;
        }

        $sheets = [];
        foreach ($this->sheets as $sheet) {
            $sheets[] = [
                'title' => $sheet['properties']->title,
                'sheet_id' => $sheet['properties']->sheetId,
                'row_count' => $sheet['properties']['gridProperties']['rowCount'],
                'column_count' => $sheet['properties']['gridProperties']['columnCount'],
            ];
        }

        return $sheets;
    }

    /**
     * @param string $SheetId SheetId - gid value from Google spreadsheet url.
     * @return string|null
     */
    public function getSheetTitleBySheetId(string $SheetId): ?string
    {
        if (!isset($this->sheets)) {
            throw new Exception('The sheets is not seted');
        }

        foreach ($this->sheets as $sheet) {
            if ($sheet['properties']->sheetId == $SheetId) {
                return $sheet['properties']->title;
            }
        }

        return null;
    }

    public function checkSheetTitleExists(string $title): bool
    {
        for ($i = 0, $size = count($this->sheets); $i < $size; $i++) {
            if ($this->sheets[$i]['properties']->title === $title) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sends batchUpdate request to Google Api
     * @param array $requests
     * @return bool
     */
    public function batchUpdateRequest(array $requests): bool
    {
        $batchUpdateRequest = new BatchUpdateSpreadsheetRequest(
            ['requests' => $requests]
        );

        try {
            $response = $this->service->spreadsheets->batchUpdate(
                $this->spreadsheet->spreadsheetId,
                $batchUpdateRequest
            );
        } catch (\Google\Service\Exception $e) {
            return false;
        }
        return true;
    }
}
