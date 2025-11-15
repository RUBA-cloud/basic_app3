<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Http\Requests\QuantityRequest;
use App\Models\Cart;
use Illuminate\Http\JsonResponse;
class CartController extends Controller
{
    //

    public function addToCart(CartRequest $request)
    {
        $validated = $request->validated();
        $userId = auth()->id();

        if(!$userId){
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }
        $exists = Cart::where('user_id', $userId)
    ->where('product_id', $validated['product_id'])
    ->when(isset($validated['color']), fn($q) => $q->where('color', $validated['color']))
    ->when(isset($validated['size_id']),  fn($q) => $q->where('size_id',  $validated['size_id']))
    ->exists();

if($exists){
    return response()->json([
        'message' => 'Product already added to favorites.',
    ], 403);
}
        $validated['user_id'] = $userId;
        $cartItem = Cart::create($validated) ;
        return response()->json([
            'status'  => 'ok',
            'message' => 'Product added to cart.',
            'data'    => $cartItem,
        ], 201);
    }

    public function updateQuantity(QuantityRequest $request)
    {
        $validated = $request->validated();
        $cartItem = Cart::findOrFail($validated['id']);
        if (!$cartItem) {
            return response()->json([
                'status'  => 'not_found',
                'message' => 'Cart item not found.',
            ], 404);
        }
        $cartItem->update([
            'quantity' => $validated['quantity'],
        ]);
        return response()->json([
            'status'  => 'ok',
            'message' => 'Cart item quantity updated.',
            'data'    => $cartItem,
        ], 200);
    }
    public function index():JsonResponse
    {
        $cart = Cart::with('size')->where('user_id', auth()->id())->with('product')->get();
        return response()->json([
            'status'  => 'ok',
            'message' => 'Cart retrieved.',
            'data'    => $cart,
        ], 200);
        // Implementation for viewing the cart
    }
    public function removeFromCart($id)
    {
        $cartItem = Cart::where('id', $id)->first();
        if (!$cartItem) {
            return response()->json([
                'status'  => 'not_found',
                'message' => 'Cart item not found.',
            ], 404);
        }
        $cartItem->delete();
        return response()->json([
            'status'  => 'ok',
            'message' => 'Product removed from cart.',
        ], 200);
    }
}
