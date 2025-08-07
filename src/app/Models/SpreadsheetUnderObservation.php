<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpreadsheetUnderObservation extends Model
{
    protected $table = 'spreadsheet_under_observations';

    public $timestamps = false;

    protected $fillable = [
        'spread_sheet_id',
        'user_id',
        'last_access',
    ];

    public function spreadsheet(): BelongsTo
    {
        return $this->belongsTo(SpreadSheet::class, 'spread_sheet_id');
    }

    /**
     * Set sort by last_access, desc - default.
     */
    #[Scope]
    protected function orderByAccess(Builder $query, bool $asc = false)
    {
        $query->orderBy('last_access', $asc ? 'asc' : 'desc');
    }
}
