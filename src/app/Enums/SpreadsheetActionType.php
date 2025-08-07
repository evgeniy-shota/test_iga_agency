<?php

namespace App\Enums;

use App\Traits\EnumGetValues;

enum SpreadsheetActionType: string
{
    use EnumGetValues;

    case Update = 'update';
    case Insert = 'insert';
    case AppendRows = 'appendRows';
    case Delete = 'delete';
    case Clear = 'clear';
}
