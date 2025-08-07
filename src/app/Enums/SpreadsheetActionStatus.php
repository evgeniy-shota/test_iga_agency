<?php

namespace App\Enums;

use App\Traits\EnumGetValues;

enum SpreadsheetActionStatus: string
{
    use EnumGetValues;

    case Executed = "executed";
    case Awaits = "awaits";
}
