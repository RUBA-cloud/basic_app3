<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    // ── Index ──────────────────────────────────────────────────────────────────
    public function index()
    {
        $brands = Brand::with(['company', 'user'])
            ->latest()
            ->paginate(20);

        return view('brand.index', compact('brands'));
    }

    // ── History (soft-deleted + all records) ───────────────────────────────────
    public function history()
    {
        $brands = Brand::withTrashed()
            ->with(['company', 'user'])
            ->latest()
            ->paginate(20);

        return view('brand.history', compact('brands'));
    }

    // ── Create ─────────────────────────────────────────────────────────────────
    public function create()
    {
        $companies = Company::select('id', 'name_en', 'name_ar')
            ->orderBy('name_en')
            ->get();

        return view('brand.create', compact('companies'));
    }

    // ── Store ──────────────────────────────────────────────────────────────────
    public function store(BrandRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('brands', 'public');
        }

        $validated['user_id'] = auth()->id();

        $brand = Brand::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Brand created successfully.',
            'data'    => new BrandResource($brand->load(['company', 'user'])),
        ], 201);
    }

    // ── Show ───────────────────────────────────────────────────────────────────
    // withTrashed so we can view soft-deleted records in history
    public function show(int $id)
    {
        $brand = Brand::withTrashed()
            ->with(['company', 'user'])
            ->findOrFail($id);

        return view('brand.show', compact('brand'));
    }

    // ── Edit ───────────────────────────────────────────────────────────────────
    public function edit(int $id)
    {
        // withTrashed: allow editing a trashed record before restoring
        $brand = Brand::withTrashed()->findOrFail($id);

        $companies = Company::select('id', 'name_en', 'name_ar')
            ->orderBy('name_en')
            ->get();

        return view('brand.edit', compact('brand', 'companies'));
    }

    // ── Update ─────────────────────────────────────────────────────────────────
    public function update(BrandRequest $request, int $id): JsonResponse
    {
        $brand     = Brand::withTrashed()->findOrFail($id);
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($brand->image) {
                Storage::disk('public')->delete($brand->image);
            }
            $validated['image'] = $request->file('image')
                ->store('brands', 'public');
        }

        $validated['user_id'] = auth()->id();

        $brand->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Brand updated successfully.',
            'data'    => new BrandResource($brand->load(['company', 'user'])),
        ]);
    }

    // ── Destroy (soft delete) ──────────────────────────────────────────────────
    public function destroy(int $id): JsonResponse
    {
        $brand = Brand::withTrashed()->findOrFail($id);

        // If already soft-deleted → force delete and remove image
        if ($brand->trashed()) {
            if ($brand->image) {
                Storage::disk('public')->delete($brand->image);
            }
            $brand->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Brand permanently deleted.',
            ]);
        }

        // First delete → soft delete only (keep image)
        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully.',
        ]);
    }

    // ── Reactivate (restore soft-deleted) ─────────────────────────────────────
    public function reactivate(int $id): JsonResponse
    {
        $brand = Brand::withTrashed()->findOrFail($id);

        if (! $brand->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'Brand is not deleted.',
            ], 422);
        }

        $brand->restore();

        return response()->json([
            'success' => true,
            'message' => 'Brand restored successfully.',
            'data'    => new BrandResource($brand->load(['company', 'user'])),
        ]);
    }

    // ── Toggle Active ──────────────────────────────────────────────────────────
    public function toggleActive(int $id): JsonResponse
    {
        $brand = Brand::withTrashed()->findOrFail($id);
        $brand->update(['is_active' => ! $brand->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Brand active status updated.',
            'data'    => new BrandResource($brand),
        ]);
    }

    // ── Toggle Top ─────────────────────────────────────────────────────────────
    public function toggleTop(int $id): JsonResponse
    {
        $brand = Brand::withTrashed()->findOrFail($id);
        $brand->update(['is_top' => ! $brand->is_top]);

        return response()->json([
            'success' => true,
            'message' => 'Brand top status updated.',
            'data'    => new BrandResource($brand),
        ]);
    }
}