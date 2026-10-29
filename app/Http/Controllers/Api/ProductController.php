<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'category' => ['nullable', 'string'],
            'subcategory' => ['nullable', 'string'],
            'featured' => ['nullable', 'boolean'],
            'is_new_arrival' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:created_date,-created_date,sort_order,-sort_order'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $products = Product::query()
            ->when($validated['category'] ?? null, fn ($query, $value) => $query->where('category', $value))
            ->when($validated['subcategory'] ?? null, fn ($query, $value) => $query->where('subcategory', $value))
            ->when(array_key_exists('featured', $validated), fn ($query) => $query->where('featured', $validated['featured']))
            ->when(array_key_exists('is_new_arrival', $validated), fn ($query) => $query->where('is_new_arrival', $validated['is_new_arrival']))
            ->tap(function ($query) use ($validated) {
                match ($validated['sort'] ?? '-created_date') {
                    'created_date' => $query->orderBy('created_at'),
                    'sort_order' => $query->orderBy('sort_order'),
                    '-sort_order' => $query->orderByDesc('sort_order'),
                    default => $query->orderByDesc('created_at'),
                };
            })
            ->limit($validated['limit'] ?? 50)
            ->get();

        return response()->json($products);
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }
}
