<?php

namespace App\Services;

use App\Enums\SpreadsheetActionStatus;
use App\Enums\SpreadsheetActionType;
use App\Models\SpreadSheetAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SpreadsheetActionService
{
    public function getAllObservationActions(
        int $spreadsheetId,
        SpreadsheetActionType|null $actionType = null,
        SpreadsheetActionStatus $status = SpreadsheetActionStatus::Awaits,
    ): Collection {
        return SpreadSheetAction::where(
            'spread_sheet_id',
            $spreadsheetId
        )
            ->where('action_status', $status->value)
            ->when($actionType, function (Builder $query) use ($actionType) {
                $query->where('action_type', $actionType);
            })->get();
    }

    public function changeStatus(
        array $ids,
        SpreadsheetActionStatus $status = SpreadsheetActionStatus::Executed
    ): bool {
        return SpreadSheetAction::whereIn('id', $ids)
            ->update(['action_status' => $status->value]);
    }

    /**
     * Add an action on a Google Sheet to a database for later execution.
     * @param int $spreadsheetId
     * @param SpreadsheetActionType $actionType
     * @param array $actionRequest Dat request ready to send to Google Api
     */
    public function createAction(
        int $spreadsheetId,
        SpreadsheetActionType $actionType,
        array $actionRequest
    ) {
        $result = SpreadSheetAction::create([
            'spread_sheet_id' => $spreadsheetId,
            'action_type' => $actionType->value,
            'action_data' => json_encode($actionRequest),
        ]);

        return $result;
    }

    public function deleteAction(int $actionId): bool
    {
        return SpreadSheetAction::where('id', $actionId)->delete();
    }

    public function deleteAllObservationActions(
        int $spreadsheetId
    ): bool {
        return SpreadSheetAction::where(
            'spread_sheet_id',
            $spreadsheetId
        )->delete();
    }
}
