<?php

namespace App\Actions;

class GetColumnsNames
{
    public static function get(int $quantity)
    {
        $alphabet = range('A', 'Z');

        if ($quantity <= count($alphabet)) {

            return array_slice($alphabet, 0, $quantity);
        }

        $quantityDifference = $quantity - count($alphabet);
        $result = $alphabet;
        $indx = 0;

        for ($i = 0; $i < $quantityDifference; $i++) {
            $prefix = (int)($i / count($alphabet));

            if ($i % count($alphabet) == 0) {
                $indx = 0;
            }

            $result[] = $alphabet[$prefix] . $alphabet[$indx];
            $indx++;
        }

        return  $result;
    }
}
