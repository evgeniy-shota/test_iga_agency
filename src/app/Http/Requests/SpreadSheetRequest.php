<?php

namespace App\Http\Requests;

use App\Rules\GoogleSpreadsheetUrl;
use Illuminate\Foundation\Http\FormRequest;

class SpreadSheetRequest extends FormRequest
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
            'url' => ['sometimes', 'url:https,http', new GoogleSpreadsheetUrl],
            'spreadsheet' => ['integer', 'numeric'],
            'sheet' => ['string', 'nullable'],
        ];
    }
}
