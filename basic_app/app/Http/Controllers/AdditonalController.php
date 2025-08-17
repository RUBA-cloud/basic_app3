<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Additonal;
use App\Models\AdditonalHistory;
use App\Http\Requests\AdditonalRequest;

class AdditonalController extends Controller
{
    public function index($isHistory = false)
    {
        if ($isHistory) {
            $additionals = AdditonalHistory::with('user')->get();
            return view('Additonal.history', compact('additionals'));
        }
        $additionals = Additonal::with('user')->where('is_active', true)->get();
        return view('Additonal.index', compact('additionals'));
    }

    public function create()
    {
        return view('Additonal.create');
    }

    public function store(AdditonalRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
           $image = $request->file('image')->store('additionals_images', 'public');
            $data['image'] = request()->getSchemeAndHttpHost() . '/storage/' . $image;
        }

        $additional = Additonal::create($data);

        if ($additional) {
            return redirect()->route('additional.index')->with('success', 'Additional product created successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to create additional product.');
        }
    }

    public function show(string $id)
    {
        $additional = Additonal::find($id) ?? AdditonalHistory::findOrFail($id);
        return view('Additonal.show', compact('additional'));
    }

    public function edit(string $id)
    {
        $additional = Additonal::findOrFail($id);
        return view('Additonal.edit', compact('additional'));
    }

    public function update(AdditonalRequest $request, string $id)
    {
        $additional = Additonal::findOrFail($id);

        $historyData = $additional->toArray();
        $historyData['user_id'] = auth()->id();
        AdditonalHistory::create($historyData);

        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('additional_images', 'public');
            $data['image'] = request()->getSchemeAndHttpHost() . '/storage/' . $imagePath;
        }

        $additional->update($data);

        return redirect()->back()->with('success', 'Additional product updated successfully.');
    }

    public function destroy(string $id)
    {
        $additional = Additonal::findOrFail($id);

        $historyData = $additional->toArray();
        $historyData['user_id'] = auth()->id();
        AdditonalHistory::create($historyData);

        $additional->delete();

        return redirect()->back()->with('success', 'Additional product deleted successfully.');
    }

    public function reactive($id)
    {
        $additionalHistory = AdditonalHistory::findOrFail($id);

        $data = $additionalHistory->toArray();
        $data['user_id'] = auth()->id();
        $data['is_active'] = true;
        unset($data['id']);

        Additonal::create($data);

        return redirect()->back()->with('success', 'Additional product reactivated successfully.');
    }
}
