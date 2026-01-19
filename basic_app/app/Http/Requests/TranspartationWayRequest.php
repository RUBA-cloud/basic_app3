<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranspartationWayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_id' => ['required', 'integer', 'exists:country,id'], // ✅ fix countries
            'city_id'    => ['required', 'integer', 'exists:cities,id'],

//            // إذا جدولك اسمه `type` بدل transpartation_types:
            'type_id'    => ['required', 'integer', 'exists:transpartation_types,id'],

            'name_en'    => ['required', 'string', 'max:255'],
            'name_ar'    => ['required', 'string', 'max:255'],

            'days_count' => ['required', 'integer', 'min:0'], // ✅ لو بدك تسمح 0 خليها min:0
            // إذا لازم >0 خليها min:1

            'is_active'  => ['nullable', 'boolean'], // ✅ checkbox safe
        ];
    }

    protected function prepareForValidation(): void
    {
        // ✅ Normalize checkbox value + ensure type_id integer
        $this->merge([
            'is_active' => $this->has('is_active') ? (int) $this->input('is_active') : 0,
            'type_id'   => $this->input('type_id') !== null ? (int) $this->input('type_id') : null,
        ]);
    }
}
