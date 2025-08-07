<?php

namespace App\Models;

use App\Enums\SpreadSheetLineStatus;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SpreadSheet extends Model
{
    /** @use HasFactory<\Database\Factories\SpreadSheetFactory> */
    use HasFactory;

    protected $table = 'spread_sheets';
    protected $fillable = [
        'spreadsheet_id',
        'url',
        'range',
        'title',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->orderByPivot('updated_at','desc');
    }

    public function sheets(): HasMany
    {
        return $this->hasMany(Sheet::class, 'spread_sheet_id');
    }

    public function observedSpreadsheet(): HasOne
    {
        return $this->hasOne(SpreadsheetUnderObservation::class, 'spread_sheet_id');
    }

    public function spreadsheetActions(): HasMany
    {
        return $this->hasMany(SpreadSheetAction::class);
    }
}
