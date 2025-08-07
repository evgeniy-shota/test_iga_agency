<?php

namespace App\Actions;

class ValidateGoogleSpreadsheetUrl
{

    private const REGEX = '^https:\/\/docs\.google\.com\/spreadsheets\/d\/[\w\d_-]+\/edit\?((usp=sharing)|(gid=[\w]+#gid=[\w]+))^';

    public static function validate(string $url)
    {
        return preg_match(self::REGEX, $url) == 1 ? true : false;
    }
}
