<?php

namespace App\Console\Commands;

use App\Actions\ParseGoogleSpreadSheetUrl;
use App\Actions\ReadCredentials;
use App\Actions\ValidateGoogleSpreadsheetUrl;
use App\Enums\SpreadSheetLineStatus;
use App\Models\SpreadsheetUnderObservation;
use App\Models\User;
use App\Services\GoogleApiClientService;
use App\Services\GoogleSheetsService;
use App\Services\GoogleSpreadsheetWorkerService;
use App\Services\SpreadSheetService;
use Google\Service\AndroidProvisioningPartner\GoogleWorkspaceAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class GetGoogleSpreadsheet extends Command
{
    public function __construct(
        protected GoogleSpreadsheetWorkerService $gSpreadsheetWorkerService,
        protected SpreadSheetService $spreadSheetService
    ) {
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-google-spreadsheet 
    {--spreadsheetUrl= : Url to Google spreadsheet}
    {--spreadSId= : Spreadsheet id}
    {--userEmail= : Email used for registration}
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
        $spreadSId = $this->option('spreadSId');
        $userEmail = $this->option('userEmail');

        if ($spreadsheetUrl) {
            if (!ValidateGoogleSpreadsheetUrl::validate($spreadsheetUrl)) {
                $this->error('Url to Google spreadsheet is invalid.');
                return;
            }
        } else {
            $userId = Auth::id();

            if (
                isset($userId)
                || (isset($userEmail)
                    && $userId = User::where('email', $userEmail)->first()->id)
            ) {
                $spreadsheets = SpreadsheetUnderObservation::where('user_id', $userId)
                    ->orderByAccess(false)->get();

                if (count($spreadsheets) == 0) {
                    $this->error('No tables found');
                    return;
                }

                $spreadsheet = $this->spreadSheetService->get($spreadsheets[0]->spread_sheet_id, $userId);
            } else if (isset($spreadSId)) {
                $spreadsheet = $this->spreadSheetService->get($spreadSId);
            }

            if (!isset($spreadsheet)) {
                $this->error('No tables found');
                $this->info('For try to getting Google spreadsheet: ');
                $this->info('Use this command with option \'--spreadsheetUrl\'=url_to_your_shreapsheet');
                $this->info('Or use this command with option \'--spreadSId\'=spread_sheet_id');
                $this->info('Also you can use this command with option \'--userEmail\'=email_used_for_registration');
                return;
            }

            $spreadsheetUrl = $spreadsheet->url;
        }

        $spreadsheet = $this->getTable($spreadsheetUrl);

        if (!$spreadsheet) {
            $this->error('Something went wrong...The table may not be available.');
            return;
        }
        if (!isset($spreadsheet['data'])) {
            $this->info('Sheet is empty');
            return;
        }

        $rows = $spreadsheet['data']['values'] ?? [];

        $size = $count && $count < count($rows)
            ? $count
            : count($rows);

        $progressbar = $this->output->createProgressBar($size);
        $progressbar->start();

        for ($i = 0, $size = $size; $i < $size; $i++) {
            $this->line('');
            // echo "\033[F\033[K]";
            // $this->info("\033[F\033[K]");
            $comment = isset($rows[$i][7]) ? $rows[$i][7] : '_______';
            $this->line("id: " . $rows[$i][0] . ' --- Comment:' . $comment);
            $progressbar->advance();
        }
        $progressbar->finish();
    }

    public function getTable(string $spreadsheetUrl)
    {
        $spreadsheet = $this->gSpreadsheetWorkerService->getSpreadsheet(
            $spreadsheetUrl,
            parseValue: false
        );

        return $spreadsheet;
    }
}
