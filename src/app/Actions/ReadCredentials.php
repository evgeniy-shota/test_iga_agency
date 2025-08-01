<?php

namespace App\Actions;

use Illuminate\Support\Facades\Storage;

class ReadCredentials
{
    public static function read(
        string $path = "Credentials/google_spreadsheets_credentials.json"
    ): false|string {

        if (Storage::disk('local')->missing($path)) {
            return false;
        }

        $credentials = Storage::disk('local')
            ->get($path);

        return $credentials;
    }
}
