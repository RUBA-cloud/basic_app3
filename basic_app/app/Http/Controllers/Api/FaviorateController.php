<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FaviorateRequest;
use App\Models\FaviorateModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FaviorateController extends Controller
{
    /**
     * قائمة المفضلة للمستخدم الحالي
     */
    public function index()
{
    // Basic lists (keep if you still need them)
    $categories = Category::where('is_active', true)->orderBy('id')->get();
    $types      = Type::where('is_active', true)->orderBy('id')->get();
    $sizes      = Size::where('is_active', true)->orderBy('id')->get();

    // First active category that HAS active products
    $firstCategory = Category::where('is_active', true)
        ->whereHas('products', fn($q) => $q->where('is_active', true))
        ->with(['products' => function ($q) {
            // select only what we need; include BOTH 'color' & 'colors'
            $q->where('is_active', true)
              ->select('id', 'category_id', 'price', 'color', 'colors');
        }])
        ->orderBy('id', 'asc')
        ->first();

    $minPrice  = null;
    $maxPrice  = null;
    $colors    = [];
    $products  = collect(); // will hold Product models collection

    if ($firstCategory) {
        $products = collect($firstCategory->products ?? []);

        // ---- Prices
        $prices = $products->pluck('price')
            ->filter(fn($p) => $p !== null && is_numeric($p))
            ->map(fn($p) => (float) $p);

        if ($prices->isNotEmpty()) {
            $minPrice = $prices->min();
            $maxPrice = $prices->max();
        }

        // ---- Colors
        // 1) Single color column (string)
        $singleColors = $products->pluck('color')
            ->filter()
            ->map(fn($c) => is_string($c) ? trim(strtolower($c)) : $c);

        // 2) JSON/array colors column
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
            ->map(fn($c) => is_string($c) ? trim(strtolower($c)) : $c);

        $colors = $singleColors->merge($jsonColors)
            ->unique()
            ->values()
            ->toArray();
    }

    // Make a lightweight products array for the response "data"
    $productsArray = $products->map(function ($p) {
        // normalize colors to array of strings
        $multi = [];
        if (is_array($p->colors)) {
            $multi = $p->colors;
        } elseif (is_string($p->colors)) {
            $decoded = json_decode($p->colors, true);
            $multi = is_array($decoded) ? $decoded : [];
        }
        return [
            'id'          => $p->id,
            'category_id' => $p->category_id,
            'price'       => $p->price !== null ? (float) $p->price : null,
            'color'       => $p->color,      // single value (if you store it)
            'colors'      => array_values(array_unique(array_map('strtolower', array_filter($multi)))),
        ];
    })->values();

    return response()->json([

        // ---- Filters + products live INSIDE 'data'
        'data' => [
            'category_id' => $firstCategory?->id,
            'min_price'   => $minPrice,
            'max_price'   => $maxPrice,
            'colors'      => $colors,
            'products'    => $productsArray,
             'status'     => true,
        'categories' => $categories,
        'types'      => $types,
        'sizes'      => $sizes,

        ]]);

}

    /**
     * إضافة منتج إلى المفضلة
     */
    public function addFaviorate(FaviorateRequest $request): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Validate request data
        $data = $request->validated();

        // Check if product is already in favorites
        $exists = FaviorateModel::where('user_id', $userId)
            ->where('product_id', $data['product_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Product already added to favorites.',
            ], 403);
        }

        // Add new favorite
        $faviorate = FaviorateModel::create([
            'user_id'    => $userId,
            'product_id' => $data['product_id'],
        ]);

        return response()->json([
            'message' => 'Product added to favorites successfully.',
            'data'    => $faviorate->load('product'),
        ], 201);
    }
    /**
     * حذف منتج من المفضلة
     */
    public function removeFaviorate(FaviorateRequest $request): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $data = $request->validated();

        $faviorate = FaviorateModel::where('user_id', $userId)
            ->where('product_id', $data['product_id'])
            ->first();

        if (!$faviorate) {
            return response()->json([
                'message' => 'Favorite not found.',
            ], 404);
        }

        $faviorate->delete();

        return response()->json([
            'message' => 'Favorite removed successfully.',
        ], 200);
    }
}
