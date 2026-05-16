<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    //

    public function index()
    {
        $brands = Brand::with(['company', 'user'])->where('is_top', true)
            ->latest()
            ->paginate(20);
        return response()->json([
            'success' => true,
            'data'    => $brands,
        ]);
    }
}
