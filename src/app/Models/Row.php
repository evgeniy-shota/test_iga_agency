<?php

namespace App\Models;

use App\Enums\SpreadSheetLineStatus;
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
        'spread_sheets_id',
        'user_id',
        'status',
        'columns',
    ];

    public function spreadSheet(): BelongsTo
    {
        return $this->belongsTo('spread_sheets');
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'date:d-m-Y',
            'updated_at' => 'datetime:H:00 d-m-Y',
        ];
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
