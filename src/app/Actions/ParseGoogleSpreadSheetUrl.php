<?php

namespace App\Actions;

/**
 * Parse
 */
class ParseGoogleSpreadSheetUrl
{
    const SPREADSHEET_ID_START = 'd/';
    const SPREADSHEET_ID_END = '/edit';
    const SHEET_ID_START = '#gid=';

    /**
     * @param string $url Google Spreadsheet URL
     * @return false|array<string,string> Array with 'spreadsheetId' and 'sheetId' keys
     */
    public static function parse(string $url): false|array
    {
        $spreadsheetIdStartPosition = strpos(
            $url,
            self::SPREADSHEET_ID_START
        ) + strlen(self::SPREADSHEET_ID_START);

        $spreadsheetIdEndPosition = strpos($url, self::SPREADSHEET_ID_END);

        $spreadsheetId =  substr(
            $url,
            $spreadsheetIdStartPosition,
            $spreadsheetIdEndPosition - $spreadsheetIdStartPosition
        );
        $sheetIdStartPosittion = strpos($url, self::SHEET_ID_START);

        $sheetId = $sheetIdStartPosittion === false ? "0"
            : substr($url,  $sheetIdStartPosittion + strlen(self::SHEET_ID_START));

        return strlen($spreadsheetId) == 0 ? false
            : [
                'spreadsheetId' => $spreadsheetId,
                'sheetId' => $sheetId
            ];
    }
}
