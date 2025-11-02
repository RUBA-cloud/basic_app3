<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterReequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
      public function rules(): array
    {
        return [
            // arrays
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],

            'types' => ['sometimes', 'array'],
            'types.*' => ['integer', 'exists:types,id'],

            'sizes' => ['sometimes', 'array'],
            'sizes.*' => ['integer', 'exists:sizes,id'],

            'colors' => ['sometimes', 'array'],
            'colors.*' => ['string'],

            // search
            'search' => ['sometimes', 'string', 'max:150'],

            // optional price range
            'price_from' => ['sometimes', 'numeric', 'min:0'],
            'price_to'   => ['sometimes', 'numeric', 'min:0'],
        ];
    }

}
