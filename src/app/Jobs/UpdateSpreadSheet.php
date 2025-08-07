<?php

namespace App\Jobs;

use App\Actions\SynchronizeSpreadsheet;
use App\Models\SpreadSheet;
use App\Services\SpreadSheetService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateSpreadSheet implements ShouldQueue
{
    use Queueable;
    /**
     * Create a new job instance.
     */
    public function __construct(
        public SpreadSheet $spreadSheet,
    ) {}

    /**
     * Execute the job of synchronize spreadsheet
     */
    public function handle(
        SynchronizeSpreadsheet $synchronizeSpreadsheet
    ): void {
        $synchronizeSpreadsheet->synchronize(
            $this->spreadSheet
        );
    }
}
