<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "rowNumber" => $this->row_number,
            "status" => $this->status,
            'name' => $this->name,
            'reserved_count' => $this->reserved_count,
            'total_count' => $this->total_count,
        ];
    }
}
