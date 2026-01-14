<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranspartationWayRequest extends FormRequest
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
            "city_id"=> "required|exists:cities,id",
            "country_id"=> "required|exists:country,id",
            "name_en"=> "required|string|max:255",
            "name_ar"=> "required|string|max:255",
            "days_count"=> "required|integer|min:1",
            "is_active"=> "boolean",
        ];
    }
}
