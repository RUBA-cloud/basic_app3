<?php

namespace App\Http\Controllers;

use App\Models\TypeHistory;
use App\Http\Requests\TypeRequest;
use App\Models\Type;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index($isHistory = false)
    {
        // If $ishistory is true, return a history view
        if ($isHistory) {
            $types = TypeHistory::with('user')->get()->sortByDesc('created_at');
            return view('type.history', compact('types'));
        }
        $types = Type::with('user')->where('is_active', 1)->get()->sortByDesc('created_at');
            return view('type.index', compact('types'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TypeRequest $request)
    {
        //
        $validated = $request->validated();

        // Create typ
        $type = Type::create($validated);
        $type->user_id = auth()->id();
        $type->save();
        return redirect()->route('type.index')->with('success', 'Type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $type = Type::findOrFail($id);
        return view('type.show', compact('type'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $type = Type::findOrFail($id);
        return view('type.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TypeRequest $request, string $id)
    {
        //
        $type = Type::findOrFail($id);
        if($type) {
        $history = $type->toArray();
        $history['user_id'] = auth()->id();
        TypeHistory::create($history);
        $type->update($request->validated());
        return redirect()->route('type.index')->with('success', 'Type updated successfully.');
        } else {
            return redirect()->route('type.index')->with('error', 'Type not found.');
        }



    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $type = Type::findOrFail($id);
        if ($type) {
            $type->is_active = false;
            $type->save();
            // Create history record
            $history = $type->toArray();
            $history['user_id'] = auth()->id();
            TypeHistory::create($history);
            // Delete the type
            $type->delete();
            return redirect()->route('type.index')->with('success', 'Type deleted successfully.');
        } else {
            return redirect()->route('type.index')->with('error', 'Type not found.');
    }
}
public function reactivate(string $id)
    {
        $type = TypeHistory::findOrFail($id);
        $type->user_id = auth()->id(); // Set the current user as the reactivating user
        $type->created_at = now(); // Reset the creation date to now
        $type->updated_at = now(); // Reset the update date to now
        $type->is_active = true;
        $type->save();

        // Prepare data for new live type
        $historyData = $type->toArray();
        unset($historyData['id']);

        // Create new live type
        $newType = Type::create($historyData);

        return redirect()->route('type.index')->with('success', 'Type reactivated successfully.');
    }
}
