<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpreadSheetAction extends Model
{
    protected $fillable = [
        'spread_sheet_id',
        'action_type',
        'action_status',
        'action_data',
    ];

    public function spreadsheet(): BelongsTo
    {
        return $this->belongsTo(SpreadSheet::class);
    }
}
