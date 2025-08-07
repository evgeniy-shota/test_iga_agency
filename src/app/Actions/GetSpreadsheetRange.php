<?php

namespace App\Actions;

class GetSpreadsheetRange
{
    public static function get(
        string $sheetTitle,
        array $columns,
        int $rowNumberStart,
        ?int $rowNumberEnd = null
    ): string|false {
        $columnsName = array_keys($columns);
        $range = "'$sheetTitle'!"
            . $columnsName[0]
            . "$rowNumberStart:"
            . $columnsName[count($columnsName) - 1]
            . ($rowNumberEnd ?? $rowNumberStart);
        return $range;
    }
}
