<?php

namespace App\Console\Commands;

use App\Actions\ParseGoogleSpreadSheetUrl;
use App\Actions\ReadCredentials;
use App\Actions\ValidateGoogleSpreadsheetUrl;
use App\Enums\SpreadSheetLineStatus;
use App\Services\GoogleApiClientService;
use App\Services\GoogleSheetsService;
use Illuminate\Console\Command;

class GetGoogleSpreadsheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-google-spreadsheet {--spreadsheetUrl= : Url to Google spreadsheet}
    {count? : number of output lines in console}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets and outputs line-by-line information about "Table" models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = $this->argument('count');
        $spreadsheetUrl = $this->option('spreadsheetUrl');

        if ($spreadsheetUrl && !ValidateGoogleSpreadsheetUrl::validate($spreadsheetUrl)) {
            $this->error('Url to Google spreadsheet is invalid.');
            return;
        }

        dump(SpreadSheetLineStatus::getValues());
        // $link = "https://docs.google.com/spreadsheets/d/1KEwADgmA0v2MdRoL1UHMx7B1MQkfejVUY3dzD0DP2xQ/edit?usp=sharing";
        // $link2 = "https://docs.google.com/spreadsheets/d/1KEwADgmA0v2MdRoL1UHMx7B1MQkfejVUY3dzD0DP2xQ/edit?gid=0#gid=0";
        $link = 'https://docs.google.com/spreadsheets/d/1KEwADgmA0v2MdRoL1UHMx7B1MQkfejVUY3dzD0DP2xQ/edit?gid=1115166090#gid=1115166090';
        // $link = 'https://docs.google.com/spreadsheets/d/1KEwADgmA0v2MdRoL1UHMx7B1MQkfejVUY3dzD0DP2xQ/edit?gid=817861958#gid=817861958';

        $data = $this->getTable($spreadsheetUrl ?? $link);

        // $progressBar = $this->withProgressBar(count($data), function ($item) {
        //     dump($item);
        // });

        $progressbar = $this->output->createProgressBar(count($data));

        $progressbar->start();
        // $this->output->progressStart(count($data));

        foreach ($data as $item) {
            sleep(0.1);
            // echo "\033[F\033[K]";
            // $this->output->write("\r" . $item[0] . " - " . $item[1], true);
            $this->info($item[0] . " - " . $item[1]);
            $progressbar->advance();
            $this->line('');
        }

        $progressbar->finish();
    }

    public function getTable(string $spreadsheetUrl)
    {
        $googleSpreadSheetIds = ParseGoogleSpreadSheetUrl::parse($spreadsheetUrl);

        $credentials = json_decode(ReadCredentials::read(), true);
        $apiClient = new GoogleApiClientService($credentials);
        $sheetsService = new GoogleSheetsService($apiClient->client);
        $sheetsService->getSpreadsheet(
            $googleSpreadSheetIds['spreadsheetId'],
            ['gid' => $googleSpreadSheetIds['sheetId']]
        );
        $sheetsService->getSheets();

        $sheetTitle = $sheetsService
            ->getSheetTitleBySheetId($googleSpreadSheetIds['sheetId']);

        $sheetsService->getSpreadsheetValues(
            $googleSpreadSheetIds['spreadsheetId'],
            $sheetTitle
        );
        dd();
    }
}
