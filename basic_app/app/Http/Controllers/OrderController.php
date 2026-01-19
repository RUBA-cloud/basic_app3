<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderStatus;
use App\Models\OrderItem;
use App\Models\OrderItemHistory;
use App\Models\TraspartationWay;
use App\Models\User;
use App\Models\Country;
use App\Models\City;

use App\Notifications\OrderStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderDeleteItemProductRequest;

class OrderController extends Controller
{
    /* ===============================
     * Helpers
     * =============================== */
    private function getOrderStatuses()
    {
        return OrderStatus::where('is_active', true)
            ->orderBy('id')
            ->get();
    }

    /* ===============================
     * Index
     * =============================== */
    public function index(Request $request)
    {
        $orderStatus = $this->getOrderStatuses();

        $orders = Order::with(['user', 'employee', 'offer'])
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', (int) $request->status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('orders.index', compact('orders', 'orderStatus'));
    }

    /* ===============================
     * Show
     * =============================== */
    public function show(Order $order)
    {
        $order->load(['user', 'employee', 'items.product', 'offer']);

        return view('orders.show', [
            'order'       => $order,
            'orderStatus' => $this->getOrderStatuses(),
        ]);
    }

    /* ===============================
     * Edit
     * =============================== */
    public function edit(Order $order)
{
    $order->load(['items.product', 'user', 'employee', 'offer']);

    $orderStatus = OrderStatus::where('is_active', true)->orderBy('id')->get();
    $employees   = User::all();
    $countries   = Country::where('is_active', true)->orderBy('id')->get();

    // ✅ selected (default from employee, but old() should override in blade)
    $employeeCountryId = optional($order->employee)->country_id;
    $employeeCityId    = optional($order->employee)->city_id;

    // ✅ cities list for the selected country (so city dropdown is filled on load)
    $cities = City::where('is_active', true)
        ->when($employeeCountryId, fn($q) => $q->where('country_id', $employeeCountryId))
        ->orderBy('id')
        ->get();

    $transparations = TraspartationWay::where('is_active', true)
        ->with(['country', 'city'])
        ->orderBy('id')
        ->get();

    return view('orders.edit', compact(
        'order',
        'orderStatus',
        'employees',
        'countries',
        'cities',
        'employeeCountryId',
        'employeeCityId',
        'transparations'
    ));
}



    /* ===============================
     * DELETE Order Item (with history)
     * =============================== */
    public function destroyItem(
        Order $order,
        OrderItem $item
    ) {
        // ✅ تأكيد أن العنصر تابع للطلب
        if ($item->order_id !== $order->id) {
            abort(403, 'Unauthorized action.');
        }

        DB::beginTransaction();

        try {
            // حفظ نسخة في history
            OrderItemHistory::create([
                'order_id'      => $order->id,
                'order_item_id' => $item->id,
                'product_id'    => $item->product_id,
                'price'         => $item->price,
                'quantity'      => $item->quantity,
                'color'         => $item->color,
                'note'          => $item->note,
                'action'        => 'deleted',
                'actor_id'      => auth()->id(),
            ]);

            $item->delete();

            DB::commit();

            return back()->with('success', __('adminlte::adminlte.item_deleted_successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Failed to delete item: ' . $e->getMessage()
            ]);
        }
    }

    /* ===============================
     * Update Order
     * =============================== */
    public function update(Request $request, Order $order)
    {
        $orderStatus = $this->getOrderStatuses();

        $data = $request->validate([
            'notes'       => ['nullable', 'string', 'max:2000'],
            'status'      => ['required', 'integer'],
            'employee_id' => ['nullable', 'exists:users,id'],
        ]);

        // Validate status id
        if (!$orderStatus->pluck('id')->contains($data['status'])) {
            return back()->withErrors(['status' => 'Invalid status'])->withInput();
        }

        // Employee required when accepted
        $acceptedStatusId = $orderStatus
            ->firstWhere('name_en', 'Accepted')
            ?->id;

        if ($acceptedStatusId && (int)$data['status'] === (int)$acceptedStatusId) {
            if (empty($data['employee_id'])) {
                return back()
                    ->withErrors(['employee_id' => 'Employee is required'])
                    ->withInput();
            }
        }

        $order->update([
            'notes'       => $data['notes'],
            'status'      => (int) $data['status'],
            'employee_id' => $data['employee_id'],
        ]);

        // Notify customer
        if ($order->user) {
            $order->user->notify(
                new OrderStatusChanged(
                    "Order #{$order->id}",
                    __('adminlte::adminlte.order_status_changed')
                )
            );
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', __('adminlte::adminlte.order_updated_successfully'));
    }

    /* ===============================
     * Delete Order
     * =============================== */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()
            ->route('orders.index')
            ->with('success', __('adminlte::adminlte.order_deleted_successfully'));
    }

    /* ===============================
     * History
     * =============================== */
    public function history()
    {
        $history = OrderHistory::with(['items.product', 'actor', 'offer'])
            ->latest()
            ->paginate(20);

        return view('orders.history', compact('history'));
    }
}
