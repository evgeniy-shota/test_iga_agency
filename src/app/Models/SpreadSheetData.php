<?php

namespace App\Models;

use App\Enums\SpreadSheetLineStatus;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpreadSheetData extends Model
{
    /** @use HasFactory<\Database\Factories\SpreadSheetDataFactory> */
    use HasFactory;

    protected $fillable = [
        'spread_sheets_id',
        'status',
        'columns',
    ];

    public function spreadSheet(): BelongsTo
    {
        return $this->belongsTo('spread_sheets');
    }

    /**
     * Only 'Allowed' status
     */
    #[Scope]
    protected function allowed(Builder $query)
    {
        $query->where('status', SpreadSheetLineStatus::Allowed->value);
    }
}
