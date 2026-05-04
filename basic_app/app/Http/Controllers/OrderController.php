<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderItem;
use App\Models\OrderItemHistory;
use App\Models\OrderStatus;
use App\Models\TraspartationWay;
use App\Models\User;
use App\Notifications\OrderStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /* ================================================================
       PRIVATE HELPERS
    ================================================================ */

    private function getOrderStatuses()
    {
        return OrderStatus::where('is_active', true)->orderBy('id')->get();
    }

    /**
     * Resolve the numeric status value for a named status (e.g. 'Accepted').
     */
    private function statusValueByName(iterable $statuses, string $nameEn): string
    {
        return (string) optional(
            collect($statuses)->firstWhere('name_en', $nameEn)
        )->status;
    }

    /* ================================================================
       INDEX
       FIX: passes metric counts, supports ?search= and ?perPage=
    ================================================================ */

    public function index(Request $request)
    {
        $orderStatus = $this->getOrderStatuses();
        $perPage     = (int) $request->get('perPage', 12);

        $orders = Order::with(['user', 'employee', 'offer', 'statusRel'])
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', (string) $request->status)
            )
            ->when($request->filled('search'), fn ($q) =>
                // extend search columns as needed
                $q->where('id', 'like', '%' . $request->search . '%')
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('orders.index', [
            'orders'          => $orders,
            'orderStatus'     => $orderStatus,
            // metric counts for hero cards
            'totalOrders'     => Order::count(),
            'pendingOrders'   => Order::where('status', '0')->count(),
            'completedOrders' => Order::where('status', '5')->count(),
            'cancelledOrders' => Order::where('status', '4')->count(),
        ]);
    }

    /* ================================================================
       SHOW
    ================================================================ */

    public function show(Order $order)
    {
        $order->load([
            'user',
            'employee',
            'items.product',
            'offer',
            'trnasparation.country',
            'trnasparation.city',
            'trnasparation.stages.country',
            'trnasparation.stages.city',
        ]);

        return view('orders.show', [
            'order'       => $order,
            'orderStatus' => $this->getOrderStatuses(),
        ]);
    }

    /* ================================================================
       EDIT
       FIX: passes both $companyCountryId AND $companyCityId
            sourced from CustomSettings helpers
    ================================================================ */

    public function edit(Order $order)
    {
        $order->load(['items.product', 'user', 'employee', 'offer', 'trnasparation']);

        $orderStatus = $this->getOrderStatuses();
        $employees   = User::orderBy('id')->get();
        $countries   = Country::where('is_active', true)->orderBy('id')->get();
        $cities      = City::where('is_active', true)->orderBy('id')->get();

        $employeeCountryId = (string) optional($order->employee)->country_id;
        $employeeCityId    = (string) optional($order->employee)->city_id;
        $userCountryId     = (string) optional($order->user)->country_id;
        $userCityId        = (string) optional($order->user)->city_id;

        // FIX: pull company country AND city from CustomSettings
        $companyCountryId  = \App\Helpers\CustomSettings::companyCountryId();
        $companyCityId     = \App\Helpers\CustomSettings::companyCityId();   // ← NEW

        return view('orders.edit', compact(
            'order',
            'orderStatus',
            'employees',
            'countries',
            'cities',
            'employeeCountryId',
            'employeeCityId',
            'userCountryId',
            'userCityId',
            'companyCountryId',   // FROM country default
            'companyCityId',      // FROM city default  ← NEW
        ));
    }

    /* ================================================================
       UPDATE
    ================================================================ */

    public function update(Request $request, Order $order)
    {
        $orderStatuses = $this->getOrderStatuses();

        /* ── 1. Base validation ──────────────────────────────────── */
        $data = $request->validate([
            'notes'                  => ['nullable', 'string', 'max:2000'],
            'status'                 => ['required'],
            'employee_id'            => ['nullable', 'integer', 'exists:users,id'],
       'transportation_id' => ['nullable', 'integer', 'exists:transportation_ways,id'],
   
   
            'from_country_id'        => ['nullable', 'integer', 'exists:country,id'],
            'from_city_id'           => ['nullable', 'integer', 'exists:cities,id'],
            'to_country_id'          => ['nullable', 'integer', 'exists:country,id'],
            'to_city_id'             => ['nullable', 'integer', 'exists:cities,id'],
            'days_count'             => ['nullable', 'integer', 'min:0'],
            'reject_reason'          => ['nullable', 'string', 'max:2000'],
        ]);

        /* ── 2. Validate status value exists in DB ───────────────── */
        $validStatuses = $orderStatuses->pluck('status')->map(fn ($v) => (string) $v)->all();
        if (!in_array((string) $data['status'], $validStatuses, true)) {
            throw ValidationException::withMessages(['status' => 'Invalid status.']);
        }

        /* ── 3. Resolve order_status_id ──────────────────────────── */
        $selectedStatus          = $orderStatuses->firstWhere('status', $data['status']);
        $data['order_status_id'] = $selectedStatus?->id;

        /* ── 4. Status-specific business rules ───────────────────── */
        $acceptedVal   = $this->statusValueByName($orderStatuses, 'Accepted');
        $rejectedVal   = $this->statusValueByName($orderStatuses, 'Rejected');
        $shippedVal    = $this->statusValueByName($orderStatuses, 'Shipped');
        $currentStatus = (string) $data['status'];

        /* Employee required when Accepted */
        if ($acceptedVal !== '' && $currentStatus === $acceptedVal && empty($data['employee_id'])) {
            throw ValidationException::withMessages([
                'employee_id' => 'Employee is required when order is accepted.',
            ]);
        }

        /* Reject reason required when Rejected */
        if ($rejectedVal !== '' && $currentStatus === $rejectedVal && empty($data['reject_reason'])) {
            throw ValidationException::withMessages([
                'reject_reason' => 'Reject reason is required when order is rejected.',
            ]);
        }

        /* Shipment fields required when Shipped */
        if ($shippedVal !== '' && $currentStatus === $shippedVal) {
            $shipmentErrors = [];
            if (empty($data['from_country_id']))  $shipmentErrors['from_country_id']  = 'From country is required.';
            if (empty($data['from_city_id']))      $shipmentErrors['from_city_id']      = 'From city is required.';
            if (empty($data['to_country_id']))     $shipmentErrors['to_country_id']     = 'To country is required.';
            if (empty($data['to_city_id']))        $shipmentErrors['to_city_id']        = 'To city is required.';
            if (empty($data['transpartation_id'])) $shipmentErrors['transpartation_id'] = 'Transportation way is required.';

            if ($shipmentErrors) {
                throw ValidationException::withMessages($shipmentErrors);
            }

            /* ── 5. Scope & stage-count validation ───────────────── */
            $way = TraspartationWay::with('stages')->find($data['transpartation_id']);

            if ($way) {
                $companyCountryId = (int) \App\Helpers\CustomSettings::companyCountryId();
                $fromCountryId    = (int) $data['from_country_id'];

                $expectedScope = ($companyCountryId && $fromCountryId === $companyCountryId)
                    ? TraspartationWay::SCOPE_LOCAL
                    : TraspartationWay::SCOPE_INTERNAL;

                if ($way->scope !== $expectedScope) {
                    $scopeLabel = $expectedScope === TraspartationWay::SCOPE_LOCAL
                        ? 'local (same-country)' : 'international';
                    throw ValidationException::withMessages([
                        'transpartation_id' => "This shipment requires a {$scopeLabel} transportation way.",
                    ]);
                }

                if ($way->isLocal() && $way->stages->count() > TraspartationWay::LOCAL_MAX_STAGES) {
                    throw ValidationException::withMessages([
                        'transpartation_id' =>
                            'Local transportation ways cannot have more than ' .
                            TraspartationWay::LOCAL_MAX_STAGES . ' stages.',
                    ]);
                }
            }
        }

        /* ── 6. Persist ──────────────────────────────────────────── */
        $order->update($data);

        /* ── 7. Notify customer ──────────────────────────────────── */
        if ($order->user) {
            try {
                $order->user->notify(new OrderStatusChanged(
                    "Order #{$order->id}",
                    __('adminlte::adminlte.order_status_changed')
                ));
            } catch (\Throwable $e) {
                logger()->warning('OrderStatusChanged notification failed: ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', __('adminlte::adminlte.order_updated_successfully'));
    }

    /* ================================================================
       DELETE ORDER ITEM  (with history)
    ================================================================ */

    public function destroyItem(Order $order, OrderItem $item)
    {
        if ($item->order_id !== $order->id) {
            abort(403);
        }

        DB::beginTransaction();
        try {
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
            return back()->withErrors(['error' => 'Failed to delete item: ' . $e->getMessage()]);
        }
    }

    /* ================================================================
       DESTROY ORDER
    ================================================================ */

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()
            ->route('orders.index')
            ->with('success', __('adminlte::adminlte.order_deleted_successfully'));
    }

    /* ================================================================
       HISTORY
    ================================================================ */

    public function history()
    {
        $history = OrderHistory::with(['items.product', 'actor', 'offer'])
            ->latest()
            ->paginate(20);

        return view('orders.history', compact('history'));
    }
}