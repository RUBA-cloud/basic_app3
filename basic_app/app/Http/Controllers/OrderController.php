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
    ================================================================ */

    public function index(Request $request)
    {
        $orderStatus = $this->getOrderStatuses();

        $orders = Order::with(['user', 'employee', 'offer'])
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', (string) $request->status)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('orders.index', compact('orders', 'orderStatus'));
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
    ================================================================ */

    public function edit(Order $order)
    {
        $order->load(['items.product', 'user', 'employee', 'offer']);

        $orderStatus = $this->getOrderStatuses();
        $employees   = User::orderBy('id')->get();
        $countries   = Country::where('is_active', true)->orderBy('id')->get();
        $cities      = City::where('is_active', true)->orderBy('id')->get();

        $employeeCountryId = (string) optional($order->employee)->country_id;
        $employeeCityId    = (string) optional($order->employee)->city_id;
        $userCountryId     = (string) optional($order->user)->country_id;
        $userCityId        = (string) optional($order->user)->city_id;

        /* Company home country — drives local/internal UI logic */
        $companyCountryId  = (string) \App\Helpers\CustomSettings::companyCountryId();

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
            'companyCountryId',
        ));
    }

    /* ================================================================
       UPDATE
    ================================================================ */

    public function update(Request $request, Order $order)
    {
        $orderStatuses = $this->getOrderStatuses();

        /* ── 1. Base validation ───────────────────────────────────── */
        $data = $request->validate([
            'notes'             => ['nullable', 'string', 'max:2000'],
            'status'            => ['required'],
            'employee_id'       => ['nullable', 'integer', 'exists:users,id'],
            'transpartation_id' => ['nullable', 'integer', 'exists:traspartation_ways,id'],
            'from_country_id'   => ['nullable', 'integer', 'exists:countries,id'],
            'from_city_id'      => ['nullable', 'integer', 'exists:cities,id'],
            'to_country_id'     => ['nullable', 'integer', 'exists:countries,id'],
            'to_city_id'        => ['nullable', 'integer', 'exists:cities,id'],
            'days_count'        => ['nullable', 'integer', 'min:0'],
            'reject_reason'     => ['nullable', 'string', 'max:2000'],
        ]);

        /* ── 2. Validate status value exists in DB ────────────────── */
        $validStatuses = $orderStatuses->pluck('status')->map(fn ($v) => (string) $v)->all();
        if (!in_array((string) $data['status'], $validStatuses, true)) {
            throw ValidationException::withMessages(['status' => 'Invalid status.']);
        }

        /* ── 3. Resolve order_status_id ──────────────────────────── */
        $selectedStatus          = $orderStatuses->firstWhere('status', $data['status']);
        $data['order_status_id'] = $selectedStatus?->id;

        /* ── 4. Status-specific business rules ───────────────────── */
        $acceptedVal = $this->statusValueByName($orderStatuses, 'Accepted');
        $rejectedVal = $this->statusValueByName($orderStatuses, 'Rejected');
        $shippedVal  = $this->statusValueByName($orderStatuses, 'Shipped');

        $currentStatus = (string) $data['status'];

        /* Employee required when Accepted */
        if ($acceptedVal !== '' && $currentStatus === $acceptedVal && empty($data['employee_id'])) {
            throw ValidationException::withMessages(['employee_id' => 'Employee is required when order is accepted.']);
        }

        /* Reject reason required when Rejected */
        if ($rejectedVal !== '' && $currentStatus === $rejectedVal && empty($data['reject_reason'])) {
            throw ValidationException::withMessages(['reject_reason' => 'Reject reason is required when order is rejected.']);
        }

        /* Shipment fields required when Shipped */
        if ($shippedVal !== '' && $currentStatus === $shippedVal) {
            $shipmentErrors = [];
            if (empty($data['from_country_id']))   $shipmentErrors['from_country_id']   = 'From country is required.';
            if (empty($data['from_city_id']))       $shipmentErrors['from_city_id']       = 'From city is required.';
            if (empty($data['to_country_id']))      $shipmentErrors['to_country_id']      = 'To country is required.';
            if (empty($data['to_city_id']))         $shipmentErrors['to_city_id']         = 'To city is required.';
            if (empty($data['transpartation_id']))  $shipmentErrors['transpartation_id']  = 'Transportation way is required.';

            if ($shipmentErrors) {
                throw ValidationException::withMessages($shipmentErrors);
            }

            /* ── 5. Scope & stage count validation ────────────────── */
            $way = TraspartationWay::with('stages')->find($data['transpartation_id']);

            if ($way) {
                $companyCountryId = (int) \App\Helpers\CustomSettings::companyCountryId();
                $fromCountryId    = (int) $data['from_country_id'];

                /*
                 * Determine expected scope:
                 *   same country as company → local (max 2 stages)
                 *   different country       → internal (unlimited stages)
                 */
                $expectedScope = ($companyCountryId && $fromCountryId === $companyCountryId)
                    ? TraspartationWay::SCOPE_LOCAL
                    : TraspartationWay::SCOPE_INTERNAL;

                /*
                 * Reject local ways for international shipments and vice-versa.
                 */
                if ($way->scope !== $expectedScope) {
                    $scopeLabel = $expectedScope === TraspartationWay::SCOPE_LOCAL
                        ? 'local (same-country)'
                        : 'international';
                    throw ValidationException::withMessages([
                        'transpartation_id' => "This shipment requires a {$scopeLabel} transportation way.",
                    ]);
                }

                /*
                 * Local ways: enforce max 2 stages.
                 */
                if ($way->isLocal() && $way->stages->count() > TraspartationWay::LOCAL_MAX_STAGES) {
                    throw ValidationException::withMessages([
                        'transpartation_id' =>
                            'Local transportation ways cannot have more than ' .
                            TraspartationWay::LOCAL_MAX_STAGES . ' stages.',
                    ]);
                }
            }
        }

        /* ── 6. Persist ───────────────────────────────────────────── */
        $order->update($data);

        /* ── 7. Notify customer ───────────────────────────────────── */
        if ($order->user) {
            try {
                $order->user->notify(new OrderStatusChanged(
                    "Order #{$order->id}",
                    __('adminlte::adminlte.order_status_changed')
                ));
            } catch (\Throwable $e) {
                // Non-critical — log but don't fail the request
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
        return redirect()->route('orders.index')
            ->with('success', __('adminlte::adminlte.order_deleted_successfully'));
    }

    /* ================================================================
       HISTORY
    ================================================================ */

    public function history()
    {
        $history = OrderHistory::with(['items.product', 'actor', 'offer'])
            ->latest()->paginate(20);

        return view('orders.history', compact('history'));
    }

    /* ================================================================
       AJAX: TRANSPORTATION WAYS SEARCH
       GET /transportation-ways/search?country_id=1&city_id=2&scope=local
    ================================================================ */

    public function searchWays(Request $request)
    {
        $countryId = (int) $request->get('country_id');
        $cityId    = (int) $request->get('city_id');

        /*
         * Determine scope from country comparison with company home country.
         * The frontend can also pass ?scope= explicitly as a hint.
         */
        $companyCountryId = (int) \App\Helpers\CustomSettings::companyCountryId();
        $scope = $request->get('scope');

        if (!$scope) {
            $scope = ($companyCountryId && $countryId === $companyCountryId)
                ? TraspartationWay::SCOPE_LOCAL
                : TraspartationWay::SCOPE_INTERNAL;
        }

        $ways = TraspartationWay::with(['type', 'stages.country', 'stages.city'])
            ->active()
            ->where('scope', $scope)
            ->when($countryId, fn ($q) => $q->where(function ($q2) use ($countryId, $cityId) {
                $q2->where('country_id', $countryId);
                if ($cityId) {
                    $q2->where(fn ($q3) =>
                        $q3->where('city_id', $cityId)->orWhereNull('city_id')
                    );
                }
            }))
            ->orderBy('name_en')
            ->get()
            ->map(function (TraspartationWay $w) {
                return [
                    'id'         => $w->id,
                    'name_en'    => $w->name_en,
                    'name_ar'    => $w->name_ar,
                    'scope'      => $w->scope,
                    'days_count' => $w->days_count,
                    'price'      => $w->price,
                    'stages'     => $w->stages->map(fn ($s) => [
                        'stage_order'    => $s->stage_order,
                        'country_id'     => $s->country_id,
                        'city_id'        => $s->city_id,
                        'transport_mode' => $s->transport_mode,
                        'days_count'     => $s->days_count,
                        'price'          => $s->price,
                        'notes'          => $s->notes,
                    ]),
                    'type' => $w->type ? [
                        'id'      => $w->type->id,
                        'name_en' => $w->type->name_en ?? '',
                        'name_ar' => $w->type->name_ar ?? '',
                    ] : null,
                ];
            });

        return response()->json(['data' => $ways]);
    }
}