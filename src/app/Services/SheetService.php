<?php

namespace App\Services;

use App\Models\Sheet;
use Illuminate\Database\Eloquent\Builder;

class SheetService
{
    public function getAll(?int $spreadsheetId = null)
    {
        $sheets = Sheet::when(
            isset($spreadsheetId),
            function (Builder $query) use ($spreadsheetId) {
                $query->where('spread_sheet_id', $spreadsheetId);
            }
        )->orderBy('id')->get();

        return $sheets;
    }

    /**
     * @param int $spreadsheetId
     * @param string $title The sheet title is unique within the spreadsheet.
     * @return App\Models\Sheet
     */
    public function getByTitle(int $spreadsheetId, string $title): Sheet
    {
        $sheet = Sheet::where('spread_sheet_id', $spreadsheetId)
            ->where('title', $title)->first();
        return $sheet;
    }

    public function get(int $id)
    {
        $sheet = Sheet::find($id);
        return $sheet;
    }

    public function getCurrent(int $spreadsheetId): Sheet
    {
        $sheet = Sheet::where('spread_sheet_id', $spreadsheetId)
            ->where('is_current', true)->first();
        return $sheet;
    }

    public function create(array $data, int $spreadsheetId)
    {
        $newSheet = Sheet::create([
            'spread_sheet_id' => $spreadsheetId,
            ...$data
        ]);
        return $newSheet;
    }

    public function massCreate(
        array $data,
        int $spreadsheetId,
        string $currentSheetTitle,
        ?string $currentRange
    ) {
        array_walk($data, function (&$item) use ($spreadsheetId, $currentSheetTitle, $currentRange) {

            if ($currentSheetTitle == $item['title']) {
                $item['range'] = $currentRange;
                $item['is_current'] = true;
                $item['is_initialized'] = true;
            } else {
                $item['range'] = null;
                $item['is_current'] = false;
                $item['is_initialized'] = false;
            }

            $item['spread_sheet_id'] = $spreadsheetId;
            $item['created_at'] = now();
            $item['updated_at'] = now();
        });

        $sheets = Sheet::insert($data);
        return $sheets;
    }

    public function update(int $id, array $data)
    {
        $sheet = Sheet::find($id);
        $sheet->update($data);
        $sheet->save();
        return $sheet;
    }

    public function massUpdate(
        array $data,
        int $spreadsheetId,
        string $currentSheetTitle,
        ?string $currentRange
    ) {
        $sheets = $this->getAll($spreadsheetId)->toArray();
        $sheetsId = array_flip(array_column($sheets, 'sheet_id'));
        $sheetsToUpdate = [];
        $sheetsToCreate = [];

        for ($i = 0, $size = count($data); $i < $size; $i++) {
            $receivedSheetId = $data[$i]['sheet_id'];

            if (isset($sheetsId[$receivedSheetId])) {
                if ($currentSheetTitle == $data[$i]['title']) {
                    $data[$i]['is_current'] = true;
                    $data[$i]['range'] = $currentRange;
                    $item[$i]['is_initialized'] = true;
                } else {
                    $data[$i]['is_current'] = false;
                }
                $sheetsToUpdate[] = array_merge($sheets[$sheetsId[$receivedSheetId]], $data[$i]);
                unset($sheetsId[$receivedSheetId]);
            } else {
                $sheetsToCreate[] = $data[$i];
            }
        }

        if (count($sheetsId) > 0) {
            $ids = Sheet::whereIn('sheet_id', array_flip($sheetsId))->select('id')->get()->toArray();
            $deleteResult = $this->massDelete(array_column($ids, 'id'));
        }

        if (count($sheetsToCreate) > 0) {
            $createResult = $this->massCreate($sheetsToCreate, $spreadsheetId, $currentSheetTitle, $currentRange);
        }

        if (count($sheetsToUpdate) > 0) {
            $updateResult = Sheet::upsert($sheetsToUpdate, uniqueBy: ['id']);
        }

        return;
    }

    public function massDelete(array $ids)
    {
        $result = Sheet::whereIn('id', $ids)->delete();
        return $result;
    }

    public function delete(int $id)
    {
        $result = Sheet::find($id)->delete();
        return $result;
    }
}
