<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'products'                      => ['required', 'array', 'min:1'],

            'products.*.product_id'         => ['required', 'integer', 'exists:products,id'],
            'products.*.size_id'            => ['required', 'integer', 'exists:sizes,id'],
            'products.*.quantity'           => ['required', 'integer', 'min:1'],
            'cart_id'            => ['required', 'integer', 'exists:carts,id'],

            'products.*.colors'             => ['required', 'array', 'min:1'],
            'products.*.colors.*'           => ['required', 'string', 'max:50'],

            // ✅ additionals per product (recommended)
            'additionals_id'     => ['nullable', 'array'],
            'additionals_id.*'   => ['integer', 'distinct', 'exists:additional,id'],
            // إذا اسم الجدول عندك مختلف عدليه: exists:product_additionals,id ... إلخ

            'address'                       => ['required', 'string', 'max:255'],
            'street_name'                   => ['required', 'string', 'max:255'],
            'building_number'               => ['required', 'string', 'max:50'],

            'lat'                           => ['required', 'numeric', 'between:-90,90'],
            'long'                          => ['required', 'numeric', 'between:-180,180'],

            'total_price'                   => ['required', 'numeric', 'min:0'],
        ];
    }
}
