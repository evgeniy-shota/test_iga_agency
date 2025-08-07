<?php

namespace App\Actions;

class GetColumnsNames
{
    public static function get(int $quantity, ?string $startedSymbol = null)
    {
        $alphabet = range('A', 'Z');

        if (!isset($startedSymbol)) {
            if ($quantity <= count($alphabet)) {
                return array_slice($alphabet, 0, $quantity);
            }

            $result = $alphabet;
            $quantityDifference = $quantity - count($alphabet);
        } else {
            if (strlen($startedSymbol) == 1) {
                $alphabetKeys = array_flip($alphabet);
                $numberStartedSymbol = $alphabetKeys[$startedSymbol];

                if (count($alphabet) - 1 - ($numberStartedSymbol) >= $quantity) {
                    return array_slice($alphabet, $numberStartedSymbol + 1, $quantity);
                }

                $result = array_slice($alphabet, $numberStartedSymbol + 1, $quantity);
                $quantity = $quantity - count($alphabet) + ($numberStartedSymbol + 1);
                $quantityDifference = $quantity;
                $findStartedSymbol = true;
            } else {
                $result = [];
                $findStartedSymbol = false;
                $quantityDifference = count($alphabet) * count($alphabet);
            }
        }

        $indx = 0;

        for ($i = 0; $i < $quantityDifference; $i++) {

            $prefix = (int)($i / count($alphabet));

            if ($i % count($alphabet) == 0) {
                $indx = 0;
            }

            $name = $alphabet[$prefix] . $alphabet[$indx];
            $indx++;

            if (isset($startedSymbol) && !$findStartedSymbol) {
                if ($startedSymbol == $name) {
                    $findStartedSymbol = true;
                    $quantityDifference =  $i + $quantity;
                }
                continue;
            }

            $result[] = $name;
        }

        return  $result;
    }
}
