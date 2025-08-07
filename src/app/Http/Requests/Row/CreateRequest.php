<?php

namespace App\Http\Requests\Row;

use App\Enums\SpreadSheetRowStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sheet_id' => ['integer', 'numeric'],
            'status' => [Rule::in(SpreadSheetRowStatus::getValues())],
            'name' => ['nullable', 'string'],
            'reserved_count' => ['nullable', 'integer', 'numeric'],
            'total_count' => ['nullable', 'integer', 'numeric'],
        ];
    }
}
