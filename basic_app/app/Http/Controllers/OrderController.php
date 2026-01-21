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
        $order->load(['user', 'employee', 'items.product', 'offer','trnasparation']);
        $orderCountryAndCity = Order::with(['trnasparation.country', 'trnasparation.city'])->find($order->id);
        return view('orders.show', [
            'order'       => $order,
            'orderStatus' => $this->getOrderStatuses(),
            'orderCountryAndCity'=> $orderCountryAndCity
        ]);
    }

    /* ===============================
     * Edit
     * =============================== */



public function edit(Order $order)
{
    $order->load(['items.product', 'user', 'employee', 'offer']);

    $orderStatus = OrderStatus::where('is_active', true)->orderBy('id')->get();
    $employees   = User::orderBy('id')->get();
    $countries   = Country::where('is_active', true)->orderBy('id')->get();
    $cities   = City::where('is_active', true)->orderBy('id')->get();

    // Defaults for employee/user (used as fallback in blade + JS)
    $employeeCountryId = (string) optional($order->employee)->country_id;
    $employeeCityId    = (string) optional($order->employee)->city_id;

    $userCountryId     = (string) optional($order->user)->country_id;
    $userCityId        = (string) optional($order->user)->city_id;

    // Single models (not collections)
    $userCountry     = $userCountryId ? Country::find($userCountryId) : null;
    $userCity        = $userCityId ? City::find($userCityId) : null;
    $employeeCountry = $employeeCountryId ? Country::find($employeeCountryId) : null;
    $employeeCity    = $employeeCityId ? City::find($employeeCityId) : null;

    return view('orders.edit', compact(
        'order',
        'orderStatus',
        'employees',
        'countries',
        'cities',

        // IDs (important for your blade/js)
        'employeeCountryId',
        'employeeCityId',
        'userCountryId',
        'userCityId',

        // models (optional if you need them in blade)
        'employeeCountry',
        'employeeCity',
        'userCountry',
        'userCity',
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
    $orderStatuses = $this->getOrderStatuses();

    $data = $request->validate([
        'notes'             => ['nullable', 'string', 'max:2000'],
        'status'            => ['required'], // نخليها مرنة (0/1/2/3 أو رقم)
        'employee_id'       => ['nullable', 'integer', 'exists:users,id'],
        'transpartation_id' => ['nullable', 'integer', 'exists:transpartation_way,id'], // ✅ عدّل اسم الجدول إذا مختلف
        'reject_reason'     => ['nullable', 'string', 'max:2000'], // لو عندك رفض
    ]);

    // ✅ Validate status value (الفورم يرسل st->status)
    $validStatuses = $orderStatuses->pluck('status')->map(fn ($v) => (string)$v)->all();
    if (!in_array((string)$data['status'], $validStatuses, true)) {
        return back()->withErrors(['status' => 'Invalid status'])->withInput();
    }

    // ✅ Employee required when Accepted (حسب status value)
    $acceptedStatusValue = (string) optional($orderStatuses->firstWhere('name_en', 'Accepted'))->status;

    if ($acceptedStatusValue !== '' && (string)$data['status'] === $acceptedStatusValue) {
        if (empty($data['employee_id'])) {
            return back()
                ->withErrors(['employee_id' => 'Employee is required'])
                ->withInput();
        }
    }

    // ✅ Reject reason required when Rejected (اختياري - إذا عندك حالة رفض)
    $rejectedStatusValue = (string) optional($orderStatuses->firstWhere('name_en', 'Rejected'))->status;
    if ($rejectedStatusValue !== '' && (string)$data['status'] === $rejectedStatusValue) {
        if (empty($data['reject_reason'])) {
            return back()
                ->withErrors(['reject_reason' => 'Reject reason is required'])
                ->withInput();
        }
    }

    // ✅ Update correctly (بدون array داخل array)
    $order->update($data);

    // ✅ Notify customer
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
