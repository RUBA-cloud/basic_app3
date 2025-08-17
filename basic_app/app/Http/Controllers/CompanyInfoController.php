<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CompanyInfoRequest;
use App\Models\CompanyInfo;
use App\Models\CompanyInfoHistory;
use Illuminate\Support\Facades\Storage;

class CompanyInfoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Show the current company information.
     */
    public function index()
    {
        $company = CompanyInfo::first();
        return view('CompanyInfo.index', [
            'company' => $company,
        ]);
    }

    /**
     * Show the company info history.
     */
    public function history()
    {
        $company = CompanyInfoHistory::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('CompanyInfo.history', compact('company'));
    }

    /**
     * Show a specific history entry.
     */
    public function show($id)
    {
        $company = CompanyInfoHistory::with('user')->findOrFail($id);

        return view('CompanyInfo.show', compact('company'));
    }

    /**
     * Store or update company info.
     */
    public function store(CompanyInfoRequest $request)
    {
        $validated = $request->validated();

        $company = CompanyInfo::first();

        // Handle logo upload
        if ($request->hasFile('image')) {
            $logoPath = $request->file('image')->store('company_logos', 'public');
            $validated['image'] = asset('storage/' . $logoPath);

            // Delete old logo if exists
            if ($company && $company->image) {
                $oldPath = str_replace(asset('storage/') . '/', '', $company->image);
                Storage::disk('public')->delete($oldPath);
            }
        }

        if ($company) {
            // Save to history
            $historyData = $company->toArray();
            unset($historyData['id']);
            $historyData['user_id'] = auth()->id();

            CompanyInfoHistory::create($historyData);

            // Update
            $company->update($validated);
        } else {
            CompanyInfo::create($validated);
        }

        return redirect()->back()->with('success', 'Company information saved successfully.');
    }

    // Empty stubs for now
    public function create() {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
    public function save(Request $request) {}
}
