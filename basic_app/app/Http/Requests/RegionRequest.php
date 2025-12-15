<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            'excepted_day_count' => $this->filled('excepted_day_count')
                ? (int) $this->input('excepted_day_count')
                : null,
        ]);
    }

    public function rules(): array
    {
        // ✅ works for route model binding {region} OR {region} as id
        $regionId = $this->route('region')?->id ?? $this->route('region') ?? null;

        return [
            'country_en' => [
                'required', 'string', 'max:120',
                Rule::unique('regions', 'country_en')->ignore($regionId),
            ],
            'country_ar' => [
                'required', 'string', 'max:120',
                Rule::unique('regions', 'country_ar')->ignore($regionId),
            ],

            'city_en'  => ['required', 'string', 'max:120'],
            'city_ar'  => ['required', 'string', 'max:120'],

            'excepted_day_count' => ['nullable', 'integer', 'min:0', 'max:365'],
            'is_active'          => ['nullable', 'boolean'],
            'user_id'            => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'country_en'         => 'country (EN)',
            'country_ar'         => 'country (AR)',
            'city_en'            => 'city (EN)',
            'city_ar'            => 'city (AR)',
            'excepted_day_count' => 'expected day count',
            'is_active'          => 'status',
            'user_id'            => 'user',
        ];
    }
}
