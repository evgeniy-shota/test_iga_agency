<?php

namespace App\Http\Requests\Row;

use App\Enums\SpreadSheetRowStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // dd($this);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => [Rule::in(SpreadSheetRowStatus::getValues())],
            'name' => ['nullable', 'string'],
            'reserved_count' => ["nullable", 'integer', 'numeric'],
            'total_count' => ["nullable", 'integer', 'numeric'],
        ];
    }
}
