<?php

namespace App\Actions;

use App\Enums\SpreadSheetRowStatus;

use function PHPUnit\Framework\returnSelf;

class ParseGoogleSpreadSheet
{
    public static function parse(array $values): array
    {
        $data = [];

        for ($i = 0, $size = count($values); $i < $size; $i++) {

            $data[$i]["row_number"] = self::checkValue($values[$i][2] ?? null, $i + 1);
            $data[$i]["status"] = self::checkValue($values[$i][3] ?? null, SpreadSheetRowStatus::Allowed->value);
            $data[$i]["name"] = self::checkValue($values[$i][4] ?? null);
            $data[$i]["reserved_count"] = self::checkValue($values[$i][5] ?? null);
            $data[$i]["total_count"] = self::checkValue($values[$i][6] ?? null);
        }

        return $data;
    }

    private static function checkValue(string|int|null $value, string|int|null $defaultValue = null)
    {
        if (!isset($value) || (is_string($value) && strlen($value) == 0)) {
            return $defaultValue;
        }

        return  $value;
    }
}
