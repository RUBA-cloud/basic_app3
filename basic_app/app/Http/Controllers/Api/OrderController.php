<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Cart;
use App\Models\CartAdditionalProduct;
use App\Models\Order;
use App\Models\OrderAddiitionalProduct;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'status'  => 'unauthenticated',
                'message' => 'Unauthenticated.',
                'data'    => [],
            ], 401);
        }

        $orders = Order::query()
            ->with([
                'items.product',
               'productAdditional',
             'offer',
                'employee:id,name,email','orderStatus',
                'trnasparation' => function ($q) {
                    $q->select([
                        'id', 'name_en', 'name_ar', 'country_id', 'city_id',
                        'days_count', 'type_id', 'is_active',
                    ])->with([
                        'country:id,name_en,name_ar',
                        'city:id,name_en,name_ar',
                        'type:id,name_en,name_ar',
                    ]);
                },
            ])
            ->where('user_id', $userId)
            ->latest('id')
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Orders list.',
            'data'    => $orders,
        ]);
    }


public function store(OrderRequest $request): JsonResponse
{
    $userId = Auth::id();

    if (!$userId) {
        return response()->json([
            'status'  => 'unauthenticated',
            'message' => 'Unauthenticated.',
            'data'    => [],
        ], 401);
    }

    $data = $request->validated();

    if (empty($data['products']) || !is_array($data['products'])) {
        return response()->json([
            'status'  => 'error',
            'message' => 'No products provided for this order.',
            'data'    => null,
        ], 422);
    }

    try {
        $order = DB::transaction(function () use ($userId, $data) {

            $orderStatusId = OrderStatus::query()
                ->where('status', 0)
                ->value('id'); // int|null

            $order = Order::create([
                'user_id'         => $userId,
                'status'          => defined(Order::class.'::STATUS_PENDING') ? Order::STATUS_PENDING : 0,
                'order_status_id' => $orderStatusId, // غيّريها لو عمودك اسمه order_status
                'address'         => $data['address'] ?? null,
                'street_name'     => $data['street_name'] ?? null,
                'building_number' => $data['building_number'] ?? null,
                'lat'             => $data['lat'] ?? null,
                'long'            => $data['long'] ?? null,
                'total_price'     => $data['total_price'],
            ]);

            $orderTotal = 0;

            foreach ($data['products'] as $productData) {
                $productId   = (int) ($productData['product_id'] ?? 0);
                $sizeId      = $productData['size_id'] ?? null;
                $quantity    = (int) ($productData['quantity'] ?? 1);
                $colors      = $productData['colors'] ?? [null];
                $additionals = $productData['additionals_id'] ?? [];
                $product = Product::query()->findOrFail($productId);
                $price     = (float) ($product->price ?? 0);
                $lineTotal = $price * $quantity;
                $orderTotal += $lineTotal;

                // ✅ items per color
                foreach ((array) $colors as $color) {
                    OrderItem::create([
                        'order_id'    => $order->id,
                        'product_id'  => $productId,
                        'size_id'     => $sizeId,
                        'color'       => $color,
                        'quantity'    => $quantity,
                        'price'       => $price,
                        'total_price' => $lineTotal,
                    ]);
                }

                // ✅ insert order additionals
                if (is_array($additionals) && !empty($additionals)) {
                    $rows = [];

                    foreach ($additionals as $additionalId) {
                        $additionalId = (int) $additionalId;
                        if ($additionalId <= 0) continue;

                        $rows[] = [
                            'order_id'      => $order->id,
                            'product_id'    => $productId,
                            'additional_id' => $additionalId,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ];
                    }

                    if (!empty($rows)) {
                        OrderAddiitionalProduct::create($rows);
                    }
                }
            }


            // ✅ IMPORTANT: delete cart_additional_product rows first, then carts
            $cartIds = Cart::query()
                ->where('user_id', $userId)
                ->pluck('id')
                ->all();

            if (!empty($cartIds)) {
                DB::table('cart_additional_product')
                    ->whereIn('cart_id', $cartIds)
                    ->delete();
            }

            Cart::query()->where('user_id', $userId)->delete();

            return $order;
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Order created successfully.',
            'data'    => $order,
        ], 201);

    } catch (\Throwable $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to create order.',
            'error'   => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}

}
