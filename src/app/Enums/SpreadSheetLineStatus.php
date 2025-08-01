<?php

namespace App\Enums;

enum SpreadSheetLineStatus: string
{
    case Allowed = 'Allowed';
    case Prohibited = 'Prohibited';

    public static function getValues()
    {
        return array_column(self::cases(), 'value');
    }
}
