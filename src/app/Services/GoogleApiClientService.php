<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;

class GoogleApiClientService
{
    public readonly Client $client;

    public function __construct(
        private readonly array|string $credentials,
        private string $appName = 'Spreadsheets Servicee',
        private string $accessType = 'offline',
        private string $scopes = Sheets::SPREADSHEETS,
        // private array $scopes = ['https://www.googleapis.com/auth/spreadsheets']
    ) {
        $this->client = new Client();
        $this->client->setApplicationName($appName);
        // $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $this->client->setScopes($scopes);
        $this->client->setAccessType($accessType);
        $this->client->setAuthConfig($credentials);
    }
}
