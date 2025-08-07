<?php

namespace App\Enums;

use App\Traits\EnumGetValues;

enum SpreadSheetRowStatus: string
{
    use EnumGetValues;

    case Allowed = 'Allowed';
    case Prohibited = 'Prohibited';
}
