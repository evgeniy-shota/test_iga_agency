<?php

namespace App\Console\Commands;

use App\Services\SpreadsheetObservationService;
use Illuminate\Console\Command;

class ObserveSpreadsheet extends Command
{
    public function __construct(
        protected SpreadsheetObservationService $sObservationService
    ) {
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:observe-spreadsheet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->sObservationService->observeSpreadsheets();
    }
}
