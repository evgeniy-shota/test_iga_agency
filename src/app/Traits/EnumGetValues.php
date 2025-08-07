<?php

namespace App\Traits;

trait EnumGetValues
{
    public static function getValues()
    {
        return array_column(self::cases(), 'value');
    }
}
