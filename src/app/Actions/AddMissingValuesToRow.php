<?php

namespace App\Actions;

class AddMissingValuesToRow
{
    public static function add(
        array $values,
        int $targetQuantity,
        string $placeholder = ''
    ): array {
        return array_merge(
            $values,
            array_fill(
                count($values),
                $targetQuantity - count($values),
                $placeholder
            )
        );
    }
}
