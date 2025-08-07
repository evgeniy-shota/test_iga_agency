<?php

namespace App\Console\Commands;

use App\Actions\SynchronizeSpreadsheet;
use App\Models\SpreadsheetUnderObservation;
use App\Services\SpreadSheetService;
use Illuminate\Console\Command;

class manualCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:manual-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(SpreadSheetService $spreadSheetService)
    {
        $spreadSheetService->synchronizeSpreadsheet(SpreadsheetUnderObservation::first()->spreadsheet);
    }
}
