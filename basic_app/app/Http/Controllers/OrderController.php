<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderStatus; // ✅ مهم
use App\Models\User;
use App\Models\Region;
use App\Notifications\OrderStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    private function getOrderStatuses()
    {
        // ✅ صح
        return OrderStatus::where('is_active', 1)
            ->orderBy('id')
            ->get();
    }

    public function index(Request $r)
    {
        $orderStatus = $this->getOrderStatuses();

        $q = Order::query()
            ->with(['user', 'employee', 'offer'])
            ->when($r->filled('status'), function ($qq) use ($r) {
                // إذا status عندك integer
                $qq->where('status', (int) $r->input('status'));
            })
            ->latest();

        return view('orders.index', [
            'orders'      => $q->paginate(15)->withQueryString(),
            'orderStatus' => $orderStatus, // ✅ تمرير صحيح
        ]);
    }

    public function show(Order $order)
    {
        $orderStatus = $this->getOrderStatuses();

        $order->load(['user', 'employee', 'items.product', 'offer']);

        return view('orders.show', [
            'order'       => $order,
            'orderStatus' => $orderStatus, // ✅
        ]);
    }

    public function edit(Order $order)
    {
       $orderStatus = $this->getOrderStatuses();

// return unique country+city pairs so each combination appears only once
$regions = Region::where('is_active', true)
    ->select('country_en', 'city_en')
    ->distinct()
    ->orderBy('country_en')
    ->orderBy('city_en')
    ->get();

// If you prefer an array grouped by country with unique cities:
// $regions = Region::where('is_active', true)
//     ->select('country', 'city')
//     ->distinct()
//     ->orderBy('country')
//     ->orderBy('city')
//     ->get()
//     ->groupBy('country')
//     ->map(fn($items) => $items->pluck('city')->values());

$order->load(['items.product']);

$employees = User::select('id', 'name')->get();

return view('orders.edit', [
    'order'       => $order,
    'employees'   => $employees,
    'orderStatus' => $orderStatus,
    'regions'     => $regions,
]);
    }

    public function update(Request $r, Order $order)
    {
        // ✅ جلب الحالات لو بتحتاجيها بالـ view عند error
        $orderStatus = $this->getOrderStatuses();

        $data = $r->validate([
            'notes'       => ['nullable', 'string', 'max:2000'],
            'status'      => ['required', 'integer'], // لو عندك statuses ديناميكية من جدول
            'employee_id' => ['nullable', 'exists:users,id'],
        ]);

        // ✅ إذا status عندك من جدول order_status بدل constants
        $validStatusIds = $orderStatus->pluck('id')->all();
        if (!in_array((int)$data['status'], $validStatusIds, true)) {
            return back()
                ->withErrors(['status' => 'Invalid status'])
                ->withInput();
        }

        // ✅ مثال: لو عندك قبول مرتبط بحالة معينة (عدّلي حسب نظامك)
        // لو لسه تستخدم constants عندك، رجّعي Rule::in([...]) بدل validation أعلاه
        // وخلّي check الموظف كما هو.

        // لو عندك شرط الموظف عندما تكون الحالة "Accepted"
        // عدّلي هذا لو عندك accepted_status_id في جدول statuses
        $acceptedStatusId = $orderStatus->firstWhere('name_en', 'Accepted')?->id; // أو id ثابت
        if ($acceptedStatusId && (int)$data['status'] === (int)$acceptedStatusId) {
            if (empty($data['employee_id'])) {
                return back()->withErrors(['employee_id' => 'Employee is required when accepting an order.'])->withInput();
            }
            $employee = User::find($data['employee_id']);
            if (!$employee) {
                return back()->withErrors(['employee_id' => 'Selected user is not permitted to handle orders.'])->withInput();
            }
        }

        $order->fill([
            'notes'  => $data['notes'] ?? $order->notes,
            'status' => (int) $data['status'],
            'employee_id' => $data['employee_id'] ?? $order->employee_id,
        ])->save();

        // Send notify to customer
        $title = "Order #{$order->id}";
        $body  = 'Your order status changed';

        $order->load('user');
        if ($order->user) {
            $order->user->notify(new OrderStatusChanged($title, $body));
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order updated & customer notified.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted.');
    }

    public function history()
    {
        $history = OrderHistory::with('items.product', 'actor', 'offer')
            ->latest()
            ->paginate(20);

        return view('orders.history', compact('history'));
    }

    function  _setText(){}

}

