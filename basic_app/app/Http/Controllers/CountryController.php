<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\CountryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countries = Country::where('is_active', true)->latest()->get();
        return view('country.index', compact('countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('country.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CountryRequest $request)
    {
        $data = $request->validated();
        $country = Country::create($data);
        if(!$country) {
             event(new \App\Events\CountryEventUpdate($country));
                    return redirect()
            ->route('country.index')
            ->with('success', 'Country created successfully.');
        }
        return redirect()
            ->route('countries.index')
            ->with('success', 'Country created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country)
    {

        return view('country.show', compact('country'));
    }
public function cities(Country $country) {
   return response()->json(['data' => City::where('country_id',$country->id)->get()]);

}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country)
    {
        return view('country.edit', compact('country'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CountryRequest $request, Country $country)
{
    $validated = $request->validated();
            $country->is_active = true;

    $this->setHistoryData($country);
    $country->update($validated);

    // ✅ إطلاق الحدث بعد التحديث
    event(new \App\Events\CountryEventUpdate($country->fresh()));

return redirect()->route('countries.index')->with('success', 'Country updated successfully.');

}


    /**
     * Remove the specified resource from storage.
     */
public function destroy(Country $country)
{
    DB::transaction(function () use ($country) {

        // 1) عطّل السجل
        $country->is_active = false;

        // 2) خزّن الهيستوري (يفضل قبل الحذف)
        $this->setHistoryData($country);
    event(new \App\Events\CountryEventUpdate($country->fresh()));

        // 3) Soft delete (إذا الموديل فيه SoftDeletes)
       Country::where('id', $country->id)->delete();
           event(new \App\Events\CountryEventUpdate($country->fresh()));

    });

    return redirect()
        ->route('countries.index')
        ->with('success', 'Country deleted successfully.');
}


    /**
     * Reactivate the specified resource.
     */
    public function reactivate($id)
    {

        $countryHistory = CountryHistory::findorFail($id);
        if($countryHistory){

            $countryHistory->is_active = true;
            $countryHistory->save();
         Country::create($countryHistory->toArray());


        return view('country.index', compact('countryHistory'));
    }}


    public function history()
    {
        $countryHistories = CountryHistory::latest()->get();
        return view('country.history', compact('countryHistories'));
    }

    public function setHistoryData($country){
          $countryHistory = $country->replicate();
         $countryHistory->user_id = auth()->id();
            CountryHistory::create($countryHistory->toArray());
    }
}
