<?php

namespace App\Models;

use App\Enums\SpreadSheetRowStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Row extends Model
{
    /** @use HasFactory<\Database\Factories\RowFactory> */
    use HasFactory;

    protected $table = 'rows';
    protected $fillable = [
        'spread_sheet_id',
        'sheet_id',
        'row_number',
        'status',
        'name',
        'reserved_count',
        'total_count',
    ];
    public $timestamps = false;

    public function sheet(): BelongsTo
    {
        return $this->belongsTo(Sheet::class);
    }

    protected function casts(): array
    {
        return [
            'status' => SpreadSheetRowStatus::class,
        ];
    }

    /**
     * Only 'Allowed' status
     */
    #[Scope]
    protected function allowed(Builder $query)
    {
        $query->where('status', SpreadSheetRowStatus::Allowed->value);
    }
}
