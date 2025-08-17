<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Size;
use App\Models\SizeHistory;
use App\Http\Requests\SizeRequest;

class SizeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index($history = false)
    {
        if ($history) {
            $sizes = SizeHistory::with('user')->orderByDesc('created_at')->paginate(10);
            return view('Size.history', compact('sizes'));
        }

        $sizes = Size::with('user')->where('is_active', 1)->orderByDesc('created_at')->paginate(10);
        return view('Size.index', compact('sizes', 'history'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Size.create');
    }
    public function show($id)
    {
        $size = Size::findOrFail($id);
        if(!$size) {
        $size = SizeHistory::findOrFail($id);
            if (!$size) {
                return redirect()->route('sizes.index')->with('error', 'Size not found.');
        }}

        return view('Size.show', compact('size'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(SizeRequest $request)
    {
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $logoPath = $request->file('image')->store('size_logo', 'public');
            $validated['image'] = request()->getSchemeAndHttpHost() . '/storage/' . $logoPath;
        }

        // Create size
        $size = Size::create($validated);
        $size->user_id = auth()->id();
        $size->save();

        return redirect()->route('sizes.index')->with('success', 'Size created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $size = Size::findOrFail($id);
        return view('Size.edit', compact('size'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SizeRequest $request, string $id)
    {
        $size = Size::findOrFail($id);
        $validated = $request->validated();

        // Store current data in history before update
        $historyData = $size->toArray();
        unset($historyData['id']); // Remove id to avoid conflict
        $historyData['user_id'] = auth()->id();
        SizeHistory::create($historyData);

        // Update current size
        $size->update($validated);
        $size->user_id = auth()->id();
        $size->save();

        return redirect()->route('sizes.index')->with('success', 'Size updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $size = Size::findOrFail($id);
        // Store current data in history before soft delete
        $historyData = $size->toArray();
        $historyData['is_active']= false;
        unset($historyData['id']);
        $historyData['user_id'] = auth()->id();
        SizeHistory::create($historyData);

        // Soft delete by marking as inactive

        $size->delete();

        return redirect()->route('sizes.index')->with('success', 'Size deleted successfully.');
    }

    /**
     * Reactivate a soft-deleted size.
     */
    public function reactive($id)
    {
        $size = SizeHistory::findOrFail($id);
        $size->is_active = true;
        $size->save();
        if ($size) {
            $newSize = $size->toArray();
            $newSize['is_active'] = true;
            $newSize['user_id'] = auth()->id();
            unset($newSize['id']); // Remove id to avoid conflict
           $siz= Size::create($newSize);
            $siz->user_id = auth()->id();
            $siz->save();
        }
        return redirect()->route('sizes.index')->with('success', 'Size reactivated successfully.');
    }
}
