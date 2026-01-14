<?php

namespace App\Http\Controllers;

use App\Http\Requests\TranspartationWayRequest;
use App\Http\Controllers\Controller;
use App\Models\TraspartationWay;
use App\Models\TraspartationWayHistory;
use \App\Events\TraspartationRequest;
use App\Models\Country;;
use App\Models\City;


class TranspartationWayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transpartationWays =TraspartationWay::where('is_active', 1)->get();
        return view("transpartationWay.index", compact("transpartationWays"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries =Country::where('is_active', 1)->get();
        $cities = City::where('is_active', 1)->get();

        return view('transpartationWay.create', compact('countries', 'cities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TranspartationWayRequest $request)
    {
        $validated = $request->validated();
        $transpartationWay = TraspartationWay::create($validated);
        event(new TranspartationWayEvent($transpartationWay));
        $transpartationWay->save();
        return redirect()->route('transpartation_ways.index');

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

if (!$transportationWay) {
    abort(404); // أو رجّعي redirect مع رسالة
}

return view('transpartationWay.show', compact('transportationWay'));


    }

    /**
     * Show the form for editing the specified resource.
     */
  public function edit(string $id)
{
    $transpartationWay = TraspartationWay::findOrFail($id);

    $countries = Country::where('is_active', 1)->get();

    // ✅ خيار 1: كل المدن
    // $cities = City::where('is_active', 1)->get();

    // ✅ خيار 2 (أفضل): فقط مدن الدولة المختارة (مع fallback)
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
        return redirect()->route('transpartation_ways.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transpartationWay = TraspartationWay::findOrFail($id);
       $this->setHistoryData($transpartationWay);
        $transpartationWay->delete();
        return redirect()->route('transpartation_ways.index');
    }

  public function setHistoryData(TraspartationWay $transpartationType)
    {
        $history = new TraspartationWayHistory();
        $history->name_en = $transpartationType->name_en;
        $history->name_ar = $transpartationType->name_ar;
        $history->days_count = $transpartationType->days_count;
        $history->is_active = $transpartationType->is_active;
        $history->user_id = auth()->id();

       TraspartationWayHistory ::create($history->toArray());
    }

    public function history(){

        $transpartationWays = TraspartationWayHistory::orderBy('created_at','desc')->paginate(10);
        return view('transpartationWay.history', compact('transpartationWays'));
    }

    public function restore($id)  {
        $transpartationWay = TraspartationWayHistory ::findOrFail($id);
       $transpartationWay->is_active = true;
       TraspartationWay::create($transpartationWay->toArray());
        return redirect()->route('transpartation_ways.index');
}
}
