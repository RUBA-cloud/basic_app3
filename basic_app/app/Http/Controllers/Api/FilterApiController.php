<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Models\Category;
use App\Models\Type;
use App\Models\Size;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilterApiController extends Controller
{
    /**
     * GET /api/filters
     * return all filter data (categories, types, sizes, colors)
     */
    public function index()
    {
        // basic lists
        $categories = Category::where('is_active', true)
            ->select('id', 'name_en', 'name_ar')
            ->orderBy('id', 'asc')
            ->get();

        $types = Type::where('is_active', true)
            ->select('id', 'name_en', 'name_ar')
            ->orderBy('id', 'asc')
            ->get();

        $sizes = Size::where('is_active', true)
            ->select('id', 'name', 'code')
            ->orderBy('id', 'asc')
            ->get();

        /**
         * COLORS:
         * case 1: products table has a single column `color`
         * case 2: products table has json/array column `colors`
         * I’ll handle both. Adjust to your real schema.
         */

        // if you store a single color per product:
        $singleColorValues = Product::where('is_active', true)
            ->whereNotNull('color')
            ->where('color', '!=', '')
            ->distinct()
            ->pluck('color')
            ->toArray();

        // if you store JSON array colors → ["red","blue"]
        // comment this block if you don't use json
        $jsonColorValues = Product::where('is_active', true)
            ->whereNotNull('colors')
            ->pluck('colors')
            ->flatMap(function ($item) {
                // try to decode JSON safely
                $decoded = json_decode($item, true);
                return is_array($decoded) ? $decoded : [];
            })
            ->unique()
            ->values()
            ->toArray();

        // merge both
        $colors = collect($singleColorValues)
            ->merge($jsonColorValues)
            ->unique()
            ->values()
            ->toArray();

        return response()->json([
            'status'     => true,
            'categories' => $categories,
            'types'      => $types,
            'sizes'      => $sizes,
            'colors'     => $colors,
        ]);
    }

    /**
     * POST /api/filters/products
     * filter products by multi category / type / size / color + search
     */
    public function filter(FilterRequest $request)
    {
        $data = $request->validated();

        $query = Product::query()->where('is_active', true);

        // 1) multiple categories
        if (!empty($data['categories'])) {
            $query->whereIn('category_id', $data['categories']);
        }

        // 2) multiple types
        if (!empty($data['types'])) {
            $query->whereIn('type_id', $data['types']);
        }

        // 3) multiple sizes
        // depends on your schema:
        // - if you have product_size pivot → we need join
        // - if product has size_id → simple whereIn
        if (!empty($data['sizes'])) {
            // simplest case: each product has one size_id
            $query->whereIn('size_id', $data['sizes']);
        }

        // 4) multiple colors
        if (!empty($data['colors'])) {
            $query->where(function ($q) use ($data) {
                foreach ($data['colors'] as $color) {
                    // if you store single color string:
                    $q->orWhere('color', $color);

                    // if you store JSON colors, also try:
                    $q->orWhereJsonContains('colors', $color);
                }
            });
        }

        // 5) search by name / description
        if (!empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // you can add price range here too:
        if (!empty($data['price_from'])) {
            $query->where('price', '>=', $data['price_from']);
        }
        if (!empty($data['price_to'])) {
            $query->where('price', '<=', $data['price_to']);
        }

        $products = $query
            ->with(['category:id,name_en,name_ar', 'type:id,name_en,name_ar', 'size:id,name,code'])
            ->orderBy('id', 'desc')
            ->paginate(20); // or ->get()

        return response()->json([
            'status'   => true,
            'products' => $products,
        ]);
    }
}
