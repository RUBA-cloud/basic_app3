<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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

                // ✅ transportation (trnasparation) + country/city/type
                'trnasparation' => function ($q) {
                    $q->select([
                        'id',
                        'name_en',
                        'name_ar',
                        'country_id',
                        'city_id',
                        'days_count',
                        'type_id',
                        'is_active',
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
}
