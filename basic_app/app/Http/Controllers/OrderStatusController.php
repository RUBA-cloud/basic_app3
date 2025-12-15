<?php

namespace App\Http\Controllers;

use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use App\Http\Requests\OrderStatusRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderStatusController extends Controller
{
    /** List order statuses */
    public function index()
    {
        $orderStatuses = OrderStatus::with('user')
            ->orderByDesc('id')
            ->paginate(5);

        return view('order_status.index', compact('orderStatuses'));
    }

    /** History list (all order statuses) */
    public function history()
    {
        $orderStatuses = OrderStatusHistory::with('user')
            ->orderByDesc('id')
            ->paginate(5);

            return view('order_status.history',$orderStatuses);
    }

    /** Show create form */
    public function create()
    {
        return view('order_status.create');
    }

    /** Search by name/active */
    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $activeFilter = $request->has('active')
            ? filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN)
            : null;

        $orderStatuses = OrderStatus::query()
            ->when($q !== '', function ($qbuilder) use ($q) {
                $qbuilder->where(function ($w) use ($q) {
                    $w->where('name_en', 'like', "%{$q}%")
                      ->orWhere('name_ar', 'like', "%{$q}%");
                });
            })
            ->when(!is_null($activeFilter), fn ($qb) => $qb->where('is_active', $activeFilter))
            ->with('user')
            ->orderByDesc('id')
            ->paginate(15)
            ->appends($request->query());

        return view('order_status.index', compact('orderStatuses', 'q', 'activeFilter'));
    }

    /** Store */

    public function storeaa(OrderStatusRequest $request){
        dd($request->validated());
    }
public function store(OrderStatusRequest $request)
{

    $data = $request->validated();

    // user who created the status
    $data['user_id'] = $data['user_id'] ?? Auth::id();

    // handle checkbox is_active (HTML checkbox may not be sent)
    $data['is_active'] = isset($data['is_active'])
        ? (bool) $data['is_active']
        : true;

    OrderStatus::create($data);

    return redirect()
        ->route('order_status.index')
        ->with('success', __('Order status created successfully.'));
}

    /** Show */
    public function show(OrderStatus $orderStatus)
    {
        return view('order_status.show', compact('orderStatus'));
    }

    /** Edit form */
    public function edit(OrderStatus $orderStatus)
    {
        return view('order_status.edit', compact('orderStatus'));
    }

    /** Update (log old/new) */
    public function update(OrderStatusRequest $request, OrderStatus $orderStatus)
    {
        $data = $$request->validated();

        DB::transaction(function () use ($orderStatus, $data) {
            $this->writeHistory($orderStatus, 'updated_before', $orderStatus->toArray());
            $orderStatus->update($data);
            broadcast(new \App\Events\OrderStatusEventUpdate($orderStatus))->toOthers();
            $this->writeHistory($orderStatus, 'updated_after', $orderStatus->fresh()->toArray());
        });

        return redirect()->route('order_status.index')
            ->with('success', __('Order status updated successfully.'));
    }

    /** "Delete": archive + deactivate (soft) */
    public function destroy(OrderStatus $orderStatus)
    {
        DB::transaction(function () use ($orderStatus) {
            $orderStatus['is_active'] =false;
            $this->writeHistory($orderStatus, 'deleted', $orderStatus->toArray());
        });

        return redirect()->route('order_status.index')
            ->with('success', __('Order status deactivated and archived to history.'));
    }

    /** Restore from a specific history row and set active */
    public function restore(OrderStatusHistory $history)
    {
        DB::transaction(function () use ($history) {
            $historyTable = (new OrderStatusHistory())->getTable();

            // Prefer FK if present
            $status = null;
            if (Schema::hasColumn($historyTable, 'orderStatus_id') && !empty($history->orderStatus_id)) {
                $status = OrderStatus::find($history->orderStatus_id);
            }

            // Fallback if no FK
            if (!$status) {
                $status = OrderStatus::where('name_en', $history->name_en ?? null)
                                     ->where('name_ar', $history->name_ar ?? null)
                                     ->first();
            }

            $payload = [
                'name_en'   => $history->name_en,
                'name_ar'   => $history->name_ar,
                'is_active' => true,
                'user_id'   => $history->user_id,
            ];

            if ($status) {
                $this->writeHistory($status, 'restored_before', $status->toArray());
                $status->update($payload);
                $this->writeHistory($status, 'restored_after', $status->fresh()->toArray());
            } else {
                $status = OrderStatus::create($payload);
                $this->writeHistory($status, 'restored_created', $status->toArray());
            }
        });

        return redirect()->route('order_status.index')
            ->with('success', __('Order status restored and activated from history.'));
    }

    /** Write a history row (adds optional columns only if they exist) */
    protected function writeHistory(OrderStatus $status, string $action, array $snapshot = []): void
    {
        $historyTable = (new OrderStatusHistory())->getTable();

        $payload = [
            'name_en'   => $snapshot['name_en']   ?? $status->name_en,
            'name_ar'   => $snapshot['name_ar']   ?? $status->name_ar,
            'is_active' => $snapshot['is_active'] ?? $status->is_active,
            'user_id'   => $snapshot['user_id']   ?? $status->user_id,
        ];

        if (Schema::hasColumn($historyTable, 'orderStatus_id')) {
            $payload['orderStatus_id'] = $status->id;
        }
        if (Schema::hasColumn($historyTable, 'action')) {
            $payload['action'] = $action;
        }

        OrderStatusHistory::create($payload);
    }
}
