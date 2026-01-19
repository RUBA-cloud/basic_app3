<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\CityRequest;
use App\Models\City;
use App\Models\CityHistory;
use App\Models\Country;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = City::with('country')->where('is_active', true)->get();
        return view('city.index', compact('cities'));
    }

    public function create()
    {
        $countries = Country::where('is_active', true)->get();
        return view('city.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CityRequest $request)
    {
        $validare = $request->validated();
        $city = City::create($validare);
        // event(new \App\Events\CityEventUpdate($city->fresh()));

        return redirect()->route('cities.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $city = City::findOrFail($id)->with('country')->first();
        if(isset($city)) {
            return view('city.show', compact('city'));
        } else {
            $city = CityHistory::findOrFail($id)->with('country')->first();
            return view('city.show', compact('city'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
    $city = City::findOrFail($id);
    $countries = Country::where('is_active', true)->get();
    return view('city.edit', compact('city', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CityRequest $request, string $id)
    {
        $city = City::findOrFail($id);
         $city->is_active = true;
         $this->setHistoryData($city);

        $city->update($request->validated());
            event(new \App\Events\CityEventUpdate($city->fresh()));

        return redirect()->route('cities.index');
    }

    public function destroy(string $id)
    {
        $city = City::findOrFail($id);
        $city->is_active = false;
        $this->setHistoryData($city);

        City::where('id', $id)->delete();
            event(new \App\Events\CityEventUpdate($city->fresh()));

        return redirect()->route('cities.index')->with('success', 'City deleted successfully.');
    }
    public function history()
    {
        $citiesHistory = CityHistory::with('country', 'user')->latest()->get();
        return view('city.history', compact('citiesHistory'));
    }

    public function setHistoryData(City $city)
    {
        $history = new CityHistory();
        $history->name_en = $city->name_en;
        $history->name_ar = $city->name_ar;
        $history->is_active = $city->is_active;
        $history->user_id = auth()->id();
        $history->country_id = $city->country_id;
        CityHistory::create($history->toArray());
    }

    public function reactivate(string $id)
    {
        $cityHistory = CityHistory::findOrFail($id);
      $cityHistory->is_active = true;
      City::create($cityHistory->toArray());
    event(new \App\Events\CityEventUpdate($cityHistory));
return redirect()->route('cities.index')->with('success', 'City reactivated successfully.');
        }
    }
