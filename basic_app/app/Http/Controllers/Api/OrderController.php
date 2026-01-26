<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
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
                'offer',
                'employee:id,name,email',
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

    /**
     * Store a new order with items.
     */
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

        // ✅ Ensure products exist
        if (empty($data['products']) || !is_array($data['products'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No products provided for this order.',
                'data'    => null,
            ], 422);
        }

        try {
            $order = DB::transaction(function () use ($userId, $data) {

                // ✅ Create order (without trusting client total_price)
                $order = Order::create([
                    'user_id'         => $userId,
                    'status'          => defined(Order::class.'::STATUS_PENDING') ? Order::STATUS_PENDING : 0,
                    'address'         => $data['address'] ?? null,
                    'street_name'     => $data['street_name'] ?? null,
                    'building_number' => $data['building_number'] ?? null,
                    'lat'             => $data['lat'] ?? null,
                    'long'            => $data['long'] ?? null,
                    'total_price'     => 0, // will be calculated
                ]);

                $orderTotal = 0;

                foreach ($data['products'] as $productData) {
                    $productId = $productData['product_id'];
                    $sizeId    = $productData['size_id'] ?? null;
                    $quantity  = (int) $productData['quantity'];
                    $colors    = $productData['colors'] ?? [null];

                    $product = Product::query()->findOrFail($productId);

                    $price = (float) ($product->price ?? 0); // adjust if your price column is different
                    $lineTotal = $price * $quantity;

                    $orderTotal += $lineTotal;

                    // ✅ Create item rows (one per color)
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
                }

                // ✅ Save server-calculated total
                $order->update([
                    'total_price' => $orderTotal,
                ]);

                // ✅ Clear cart
                Cart::query()->where('user_id', $userId)->delete();

                return $order;
            });

            // ✅ Reload relationships
            $order->load([
                'items.product',
                'offer',
                'employee:id,name,email',
                'trnasparation.country:id,name_en,name_ar',
                'trnasparation.city:id,name_en,name_ar',
                'trnasparation.type:id,name_en,name_ar',
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Order created successfully.',
                'data'    => $order,
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create order.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
