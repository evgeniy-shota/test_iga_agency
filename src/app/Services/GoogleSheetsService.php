<?php

namespace App\Services;

use Error;
use Exception;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\Sheet;
use Google\Service\Sheets\Spreadsheet;
use Google\Service\Sheets\ValueRange;
use Psy\Exception\ThrowUpException;

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
        $this->spreadsheet = $this->service->spreadsheets->get($spreadsheetId);
    }

    /**
     * Get Google spreadsheet values from range
     * @param string $spreadsheetId SpreadsheetId is a spreadsheet id from Google spreadsheet url
     */
    public function getSpreadsheetValues(string $spreadsheetId, string $range)
    {
        $this->spreadsheetValues = $this->service->spreadsheets_values->get($spreadsheetId, $range);
        dump($this->spreadsheetValues);
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

    public function spreadsheetValues()
    {
        return $this->spreadsheetValues ?? null;
    }

    /**
     * @return array<\Google\Service\Sheets\Sheet>
     */
    public function sheets(): ?array
    {
        return $this->sheets ?? null;
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
}
