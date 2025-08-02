<?php

namespace App\Models;

use App\Enums\SpreadSheetLineStatus;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SpreadSheet extends Model
{
    /** @use HasFactory<\Database\Factories\SpreadSheetFactory> */
    use HasFactory;

    protected $table = 'spread_sheets';
    protected $fillable = [
        'user_id',
        'url',
        'sheets',
        'current_sheet',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo('users');
    }

    public function sheetData(): HasOne
    {
        return $this->hasOne('spread_sheet_data');
    }
}
