<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest; // fixed typo
use App\Models\Category;
use App\Models\Type;
use App\Models\Size;
use App\Models\Product;
use Illuminate\Http\Request;

class FilterApiController extends Controller
{
    /**
     * GET /api/filters
     * Returns all filter data (categories, types, sizes) and
     * derived filters (min/max price, colors) from the first category that has products.
     */
    public function index()
    {
        // Basic lists
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name_en', 'name_ar', 'is_active', 'image']);

        $types = Type::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name_en', 'name_ar', 'is_active']);

        $sizes = Size::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name_en', 'name_ar', 'is_active']);

        // First active category that HAS active products
        $firstCategory = Category::query()
            ->where('is_active', true)
            ->whereHas('products', fn ($q) => $q->where('is_active', true))
            ->with(['products' => function ($q) {
                $q->where('is_active', true)
                  ->select('id', 'category_id', 'price',  'colors'); // color (string) + colors (json)
            }])
            ->orderBy('id')
            ->first();

        $minPrice = null;
        $maxPrice = null;
        $colors   = [];
        $categoryId = $firstCategory?->id;

        if ($firstCategory) {
            $products = collect($firstCategory->products);

            // Prices
            $prices = $products->pluck('price')
                ->filter(fn ($p) => $p !== null && is_numeric($p))
                ->map(fn ($p) => (float) $p);

            if ($prices->isNotEmpty()) {
                $minPrice = $prices->min();
                $maxPrice = $prices->max();
            }

            // Colors from 'color' (single string)
            $singleColors = $products->pluck('color')
                ->filter()
                ->map(fn ($c) => is_string($c) ? trim(strtolower($c)) : $c);

            // Colors from 'colors' (json/array)
            $jsonColors = $products->pluck('colors')
                ->flatMap(function ($val) {
                    if (is_array($val)) {
                        return $val;
                    }
                    if (is_string($val)) {
                        $decoded = json_decode($val, true);
                        return is_array($decoded) ? $decoded : [];
                    }
                    return [];
                })
                ->filter()
                ->map(fn ($c) => is_string($c) ? trim(strtolower($c)) : $c);

            $colors = $singleColors->merge($jsonColors)
                ->unique()
                ->values()
                ->toArray();
        }

        // Order: status, data, products (no products here by spec)
        return response()->json([
            'status' => true,
            'data'   => [
                'categories'  => $categories,
                'types'       => $types,
                'sizes'       => $sizes,
                'category_id' => $categoryId,
                'min_price'   => $minPrice,
                'max_price'   => $maxPrice,
                'colors'      => $colors,
            ],
        ]);
    }

    /**
     * POST /api/filters/products
     * Filter products by multi category/type/size/color + search + price range.
     * Returns: status, data (meta), products (items).
     */
    public function filter(FilterRequest $request)
    {
        $data  = $request->validated();
        $query = Product::query()->where('is_active', true);

        // 1) multiple categories
        if (!empty($data['categories']) && is_array($data['categories'])) {
            $query->whereIn('category_id', $data['categories']);
        }

        // 2) multiple types
        if (!empty($data['types']) && is_array($data['types'])) {
            $query->whereIn('type_id', $data['types']);
        }

        // 3) multiple sizes
        // If you have a pivot (product_size), replace with join/whereHas accordingly.
        if (!empty($data['sizes']) && is_array($data['sizes'])) {
            $query->whereIn('size_id', $data['sizes']);
        }

        // 4) multiple colors (supports 'color' (string) and 'colors' (json))
        if (!empty($data['colors']) && is_array($data['colors'])) {
            $colors = array_values(array_unique(array_map(
                fn ($c) => is_string($c) ? trim(strtolower($c)) : $c,
                $data['colors']
            )));

            $query->where(function ($q) use ($colors) {
                foreach ($colors as $color) {
                    // single color column
                    $q->orWhere('color', $color);

                    // JSON array column
                    // requires MySQL 5.7+/MariaDB 10.2+ and JSON column type
                    $q->orWhereJsonContains('colors', $color);
                }
            });
        }

        // 5) search by name/description (both EN/AR if present)
        if (!empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 6) price range
        if (!empty($data['price_from']) && is_numeric($data['price_from'])) {
            $query->where('price', '>=', (float) $data['price_from']);
        }
        if (!empty($data['price_to']) && is_numeric($data['price_to'])) {
            $query->where('price', '<=', (float) $data['price_to']);
        }

        // Eager loads — adjust relation names to your app
        $perPage  = (int) ($data['per_page'] ?? 20);
        $products = $query
            ->with(['category:id,name_en,name_ar', 'type:id,name_en,name_ar', 'sizes']) // change if different
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->query()); // keep query string in pagination links

        // Order: status, data, products
        return response()->json([
            'status'   => true,
            'data'     => [
                'count'        => $products->total(),
                'current_page' => $products->currentPage(),
                'per_page'     => $products->perPage(),
                'last_page'    => $products->lastPage(),
                'has_more'     => $products->hasMorePages(),
                            'products' => $products->items(),

            ],
            // If you still want full paginator URLs, expose them separately:
            'pagination' => [
                'first_page_url' => $products->url(1),
                'last_page_url'  => $products->url($products->lastPage()),
                'next_page_url'  => $products->nextPageUrl(),
                'prev_page_url'  => $products->previousPageUrl(),
            ],
        ]);
    }
}
