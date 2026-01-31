<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Http\Requests\QuantityRequest;
use App\Models\Cart;
use App\Models\CartAdditionalProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(): JsonResponse
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'status'  => 'unauthenticated',
                'message' => 'Unauthenticated.',
                'data'    => [],
            ], 401);
        }

        return $this->buildCartResponse($userId, 'Cart retrieved.');
    }

    public function addToCart(CartRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'status'  => 'unauthenticated',
                'message' => 'Unauthenticated.',
                'data'    => [],
            ], 401);
        }

        // ✅ check exists (product + color + size)
        $exists = Cart::where('user_id', $userId)
            ->where('product_id', $validated['product_id'])
            ->when(array_key_exists('color', $validated) && $validated['color'] !== null, fn ($q) => $q->where('color', $validated['color']))
            ->when(array_key_exists('size_id', $validated) && $validated['size_id'] !== null, fn ($q) => $q->where('size_id', $validated['size_id']))
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'exists',
                'message' => 'Product already added to cart.',
                'data'    => [],
            ], 409);
        }

        try {
            DB::beginTransaction();

            $validated['user_id'] = $userId;

            // ✅ create cart item
            $cart = Cart::create($validated);

            // ✅ if additionals exist, attach them
            $additionals = $validated['additionals_id'] ?? null;

            if (is_array($additionals) && count($additionals) > 0) {
                $rows = [];

                foreach ($additionals as $additionalId) {
                    if ($additionalId === null) continue;

                    $rows[] = [
                        'cart_id'       => $cart->id,
                        'product_id'    => $validated['product_id'],
                        'additional_id' => (int) $additionalId,
                    ];
                }

                if (!empty($rows)) {
                    CartAdditionalProduct::insert($rows);
                }
            }

            DB::commit();

            return $this->buildCartResponse($userId, 'Product added to cart.', 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to add product to cart.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function updateQuantity(QuantityRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'status'  => 'unauthenticated',
                'message' => 'Unauthenticated.',
                'data'    => [],
            ], 401);
        }

        $cartItem = Cart::where('id', $validated['id'])
            ->where('user_id', $userId)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'status'  => 'not_found',
                'message' => 'Cart item not found.',
                'data'    => [],
            ], 404);
        }

        $cartItem->update([
            'quantity' => $validated['quantity'],
        ]);

        return $this->buildCartResponse($userId, 'Cart item quantity updated.');
    }

    public function removeFromCart(Request $request): JsonResponse
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'status'  => 'unauthenticated',
                'message' => 'Unauthenticated.',
                'data'    => [],
            ], 401);
        }

        $cartItem = Cart::where('id', $request->id)
            ->where('user_id', $userId)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'status'  => 'not_found',
                'message' => 'Cart item not found.',
                'data'    => [],
            ], 404);
        }

        try {
            DB::beginTransaction();

            // ✅ delete related additionals rows first
            CartAdditionalProduct::where('cart_id', $cartItem->id)->delete();

            // ✅ delete cart item
            $cartItem->delete();

            DB::commit();

            return $this->buildCartResponse($userId, 'Product removed from cart.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to remove product from cart.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    protected function buildCartResponse(
        int $userId,
        string $message = 'Cart retrieved.',
        int $statusCode = 200
    ): JsonResponse {
        // ✅ load cart + additional products (requires relations on Cart model)
        $cart = Cart::with([
                'product',
                'size',
                'cartAdditional', // keep your method name (typo) as-is
            ])
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'status'  => 'ok',
            'message' => $message,
            'data'    => $cart,
        ], $statusCode);
    }
}
