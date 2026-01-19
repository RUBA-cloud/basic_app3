<?php

namespace App\Http\Controllers;

use App\Models\TranspartationType;
use Illuminate\Http\Request;
use App\Http\Requests\TranspartationWayRequest;

use App\Models\TraspartationWay;
use App\Models\TraspartationWayHistory;
use App\Models\Country;
use App\Models\City;
use Illuminate\Support\Facades\DB;

// ✅ عدلي الاسم إذا Event عندك اسمها مختلف
use App\Events\TranspartationWayEventUpdated;

class TranspartationWayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transpartationWays = TraspartationWay::where('is_active', 1)->get();
        return view("transpartationWay.index", compact("transpartationWays"));
    }

    /**
     * AJAX: search transportation ways by country/city
     */
    public function search(Request $request)
    {

         $ways = TraspartationWay::where("is_active",1)->where("country_id", $request->country_id)->where("city_id",$request->city_id)->get();



        return response()->json([
            'status' => true,
            'data'   => $ways,
       ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::where('is_active', 1)->get();
        $cities    = City::where('is_active', 1)->get();
        $transpartationsType = TranspartationType::where('is_active',1 )->get();

        return view('transpartationWay.create', compact('countries', 'cities','transpartationsType'));
    }

    /**
     * Store a newly created resource in storage.
     */


public function store(TranspartationWayRequest $request)
{
    $validated = $request->validated();

    // ✅ normalize type field (support both names)
    // إذا الفورم يرسل type_id => ممتاز
    // إذا يرسل transpartation_type_id => نحوله إلى type_id
    if (empty($validated['type_id']) && !empty($validated['transpartation_type_id'])) {
        $validated['type_id'] = $validated['transpartation_type_id'];
    }
    unset($validated['transpartation_type_id']);

    // ✅ ensure active default
    $validated['is_active'] = isset($validated['is_active']) ? (int) $validated['is_active'] : 1;

    // ✅ FK safety: confirm type exists (table name based on your FK error: `type`)
    $typeId = (int) ($validated['type_id'] ?? 0);
    if ($typeId <= 0 || !DB::table('type')->where('id', $typeId)->exists()) {
        return back()
            ->withInput()
            ->withErrors(['type_id' => __('adminlte::adminlte.invalid_type') ?? 'Invalid type selected.']);
    }

    // ✅ create
    $transpartationWay = TraspartationWay::create($validated);

    // ✅ broadcast / event
    event(new TranspartationWayEventUpdated($transpartationWay));

    return redirect()
        ->route('transpartation_ways.index')
        ->with('success', __('adminlte::adminlte.saved_successfully'));
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transportationWay = TraspartationWay::with(['country', 'city'])->find($id);

        if (!$transportationWay) {
            $transportationWay = TraspartationWayHistory::with(['country', 'city'])->find($id);
        }

        abort_if(!$transportationWay, 404);

        return view('transpartationWay.show', compact('transportationWay'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $transpartationWay = TraspartationWay::findOrFail($id);
        $countries = Country::where('is_active', 1)->get();

        // ✅ الأفضل: مدن الدولة المختارة فقط
        $citiesQuery = City::where('is_active', 1);
        if (!empty($transpartationWay->country_id)) {
            $citiesQuery->where('country_id', $transpartationWay->country_id);
        }
        $cities = $citiesQuery->get();

        return view('transpartationWay.edit', compact('transpartationWay', 'countries', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TranspartationWayRequest $request, string $id)
    {
        $transpartationWay = TraspartationWay::findOrFail($id);
        $validated = $request->validated();

        $transpartationWay->update($validated);

        // ✅ broadcast / event after update
        event(new TranspartationWayEventUpdated($transpartationWay));

        return redirect()
            ->route('transpartation_ways.index')
            ->with('success', __('adminlte::adminlte.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transpartationWay = TraspartationWay::findOrFail($id);

        $this->setHistoryData($transpartationWay);

        $transpartationWay->delete();

        return redirect()
            ->route('transpartation_ways.index')
            ->with('success', __('adminlte::adminlte.deleted_successfully'));
    }

    /**
     * Save history snapshot
     */
    public function setHistoryData(TraspartationWay $transpartationWay)
    {
        TraspartationWayHistory::create([
            'name_en'    => $transpartationWay->name_en,
            'name_ar'    => $transpartationWay->name_ar,
            'days_count' => $transpartationWay->days_count,
            'country_id' => $transpartationWay->country_id,
            'city_id'    => $transpartationWay->city_id,
            'is_active'  => $transpartationWay->is_active,
            'user_id'    => auth()->id(),
        ]);
    }

    public function history()
    {
        $transpartationWays = TraspartationWayHistory::orderBy('created_at', 'desc')->paginate(10);
        return view('transpartationWay.history', compact('transpartationWays'));
    }

    public function restore($id)
    {
        $history = TraspartationWayHistory::findOrFail($id);

        $data = $history->toArray();
        unset($data['id']); // ✅ مهم عشان ما يحاول يرجّع نفس ID
        $data['is_active'] = 1;

        TraspartationWay::create($data);

        return redirect()
            ->route('transpartation_ways.index')
            ->with('success', __('adminlte::adminlte.restored_successfully'));
    }
}
