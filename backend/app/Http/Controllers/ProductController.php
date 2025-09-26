<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $with = collect(explode(',', (string) $request->query('with')))
        ->intersect(['variants','images'])->all();
        $q = Product::query()->where('is_archived', false)->orderByDesc('id');
        if ($s = $request->query('search')) { $q->where('title', 'like', "%{$s}%"); }
        if ($with) { $q->with($with); }
        return response()->json($q->paginate(20));
    }


    public function show(int $id, Request $request): JsonResponse
    {
        $with = collect(explode(',', (string) $request->query('with')))
        ->intersect(['variants','images'])->all();
        $q = Product::query(); if ($with) $q->with($with);
        $product = $q->findOrFail($id);
        return response()->json($product);
    }
}
