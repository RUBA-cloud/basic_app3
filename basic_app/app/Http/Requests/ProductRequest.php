<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'user_id' => 'nullable|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'type_id' => 'nullable|exists:type,id',
            'size_id' => 'nullable|exists:sizes,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate multiple images
            'colors.*' => 'nullable', // Validate multiple colors
            'sizes.*' => 'nullable|exists:sizes,id', // Validate multiple sizes
            'additional.*' => 'nullable|exists:additonal,id', // Validate multiple additional items
            //
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
