<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
        // Grab the current category ID from the route (for ignore on update)
        $categoryId = $this->route('categories');

        return [
            'name_en' => [
                'required',
                'string',
                'max:25',
                Rule::unique('categories', 'name_en')
                    ->ignore($categoryId),
            ],

            'name_ar' => [
                'required',
                'string',
                'max:25',
                Rule::unique('categories', 'name_ar')
                    ->ignore($categoryId),
            ],

            'is_active'  => ['boolean'],

            'branch_ids'   => ['nullable', 'array'],
            // Ensure each branch ID actually exists in company_branches
            'branch_ids.*' => ['integer', 'exists:company_branches,id'],

            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }
}
