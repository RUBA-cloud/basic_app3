<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class CategoryController extends Controller
{
    /**
     * GET /api/categories
     * Paginated list of active categories with products.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->query('per_page', 10));

        try {
            $categories = Category::with('products')
                ->where('is_active', true)
                ->paginate($perPage);

            return response()->json([
                'status'  => 'ok',
                'message' => 'Categories retrieved.',
                'data'    => $categories,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to retrieve categories.',
                'error'   => app()->hasDebugModeEnabled() ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET /api/categories/{id}
     * Single active category with products.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $category = Category::with('products')
                ->where('is_active', true)
                ->find($id);

            if (!$category) {
                return response()->json([
                    'status'  => 'not_found',
                    'message' => 'Category not found or inactive.',
                    'data'    => null,
                ], 404);
            }

            return response()->json([
                'status'  => 'ok',
                'message' => 'Category retrieved.',
                'data'    => $category,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to retrieve category.',
                'error'   => app()->hasDebugModeEnabled() ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Example search by optional filters (name, active).
     * GET /api/categories/search?name=...&active=1
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = Category::with('products');

            if ($request->filled('active')) {
                $query->where('is_active', (bool) $request->boolean('active'));
            }

            if ($request->filled('name')) {
                $query->where('name', 'like', '%'.$request->query('name').'%');
            }

            $perPage    = (int) ($request->query('per_page', 10));
            $categories = $query->paginate($perPage);

            return response()->json([
                'status'  => 'ok',
                'message' => 'Search results.',
                'data'    => $categories,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Search failed.',
                'error'   => app()->hasDebugModeEnabled() ? $e->getMessage() : null,
            ], 500);
        }
    }
}
