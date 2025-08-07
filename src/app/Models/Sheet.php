<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sheet extends Model
{
    /** @use HasFactory<\Database\Factories\SheetFactory> */
    use HasFactory;

    protected $table = 'sheets';
    protected $fillable = [
        'spread_sheet_id',
        'sheet_id',
        'title',
        'range',
        'row_count',
        'column_count',
        'is_current',
        'is_initialized',
    ];

    public function spreadsheet(): BelongsTo
    {
        return $this->belongsTo(SpreadSheet::class, 'spread_sheet_id');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(Row::class);
    }
}
