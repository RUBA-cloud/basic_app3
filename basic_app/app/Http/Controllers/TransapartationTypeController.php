<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\TranspartationTypeRequest;
use App\Models\TranspartationType;

use App\Models\TranspartationTypeHistory;

class TransapartationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transpartationType = TranspartationType::with('user')->where('is_active', true)->get();

        return view('transpartationType.index', compact('transpartationType'));}

    public function create()
    {
        return view('transpartationType.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TranspartationTypeRequest $request)
    {
        $transpartation = TranspartationType::create($request->validated());
          event(new \App\Events\TranspartationEventUdpdated($transpartation->fresh()));

        return redirect()->route('transpartation_types.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transpartationType = TranspartationType::findOrFail($id)->first();
        if(isset($transpartationType)) {
            return view('transpartationType.show', compact('transpartationType'));
        } else {
            $transpartationType = TranspartationTypeHistory::findOrFail($id)->first();
            return view('transpartationType.show', compact('transpartationType'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
    $transpartationType = TranspartationType::findOrFail($id);
    return view('transpartationType.edit', compact('transpartationType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TranspartationTypeRequest $request, string $id)
    {
        $transpartation = TranspartationType::findOrFail($id);
         $transpartation->is_active = true;
         $this->setHistoryData($transpartation);
        $transpartation->update($request->validated());
          event(new \App\Events\TranspartationEventUdpdated($transpartation->fresh()));

        return redirect()->route('transpartation_types.index')->with('success', 'transpartation updated successfully.s');
    }

    public function destroy(string $id)
    {
        $transpartation = TranspartationType::findOrFail($id);
        $transpartation->is_active = false;
        $this->setHistoryData($transpartation);

        TranspartationType::where('id', $id)->delete();
            event(new \App\Events\TranspartationEventUdpdated($transpartation->fresh()));
        return redirect()->route('transpartation_types.index')->with('success', 'transpartation deleted successfully.');
    }
    public function history()
    {
        $transpartation = TranspartationTypeHistory::with('user')->latest()->get();
        return view('transpartationType.history', compact('transpartation'));
    }

    public function setHistoryData(TranspartationType $transpartationType)
    {
        $history = new TranspartationTypeHistory();
        $history->name_en = $transpartationType->name_en;
        $history->name_ar = $transpartationType->name_ar;
        $history->is_active = $transpartationType->is_active;
        $history->user_id = auth()->id();

        TranspartationTypeHistory::create($history->toArray());
    }

    public function reactivate(string $id)
    {
        $transpartationTypeHistory = TranspartationTypeHistory::findOrFail($id);
      $transpartationTypeHistory->is_active = true;
      TranspartationType::create($transpartationTypeHistory->toArray());
      event(new \App\Events\TranspartationEventUdpdated($transpartationTypeHistory));return redirect()->route('transpartation_types.index')->with('success', 'transpartation reactivated successfully.');
        }
    }
