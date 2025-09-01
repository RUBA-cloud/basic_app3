<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Get the current category id for ignoring on update
        $categoryId = $this->route('categories');

        return [
            'name_en' => [
                'required',
                'string',
                'max:25',
                Rule::unique('categories', 'name_en')->ignore($categoryId),
            ],
            'name_ar' => [
                'required',
                'string',
                'max:25',
                Rule::unique('categories', 'name_ar')->ignore($categoryId),
            ],
            'is_active' => ['boolean'],

            'branch_ids'   => ['nullable', 'array'],
            'branch_ids.*' => ['integer', 'exists:company_branches,id'],

            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }
  public function attributes(): array
    {
        return trans('adminlte::validation.attributes');
    }

    public function messages(): array
    {
        return trans('adminlte::validation.messages');
    }
}
