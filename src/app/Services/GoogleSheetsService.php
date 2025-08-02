<?php

namespace App\Services;

use App\Actions\AddMissingValuesToRow;
use App\Actions\GetColumnsNames;
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
    public function getSpreadsheetValues(string $spreadsheetId, string $range)
    {
        $this->spreadsheetValues = $this->service->spreadsheets_values->get($spreadsheetId, $range);
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
        if (!isset($this->spreadsheetValues)) {
            return null;
        }

        $range = $this->spreadsheetValues->range;
        $values = [];
        $size = count($this->spreadsheetValues->values);
        $maxCols = count($this->spreadsheetValues->values[0]);

        for ($i = 1; $i < $size; $i++) {
            $countValues = count($this->spreadsheetValues->values[$i]);

            if ($countValues > $maxCols) {
                $maxCols = $countValues;
            }
        }

        $columnsName = GetColumnsNames::get($maxCols);

        for ($i = 1; $i < $size; $i++) {
            $countCurrentRow = count($this->spreadsheetValues->values[$i]);

            $values[] = array_combine(
                $columnsName,
                $countCurrentRow < count($columnsName)
                    ? AddMissingValuesToRow::add(
                        $this->spreadsheetValues->values[$i],
                        count($columnsName)
                    )
                    : $this->spreadsheetValues->values[$i],
            );
        }

        return ([
            'range' => $range,
            'columns' => $columnsName,
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
            $sheets[$sheet['properties']->title] = $sheet['properties']->sheetId;
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

    public function checkSheetTitleExists(string $title)
    {
        for ($i = 0, $size = count($this->sheets); $i < $size; $i++) {
            if ($this->sheets[$i]['properties']->title === $title) {
                return true;
            }
        }

        return false;
    }
}
