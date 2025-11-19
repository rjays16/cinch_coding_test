<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class PublicProductController extends Controller
{
    /**
     * Display a listing of all active products (public).
     */
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)
            ->with('seller:id,store_name');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSortFields = ['name', 'price', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        if ($request->has('per_page') && $request->per_page === 'all') {
            $products = $query->get();
        } else {
            $perPage = $request->get('per_page', 12);
            $products = $query->paginate($perPage);
        }

        // Add image_url to each product
        if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $products->getCollection()->transform(function ($product) {
                if ($product->image) {
                    $product->image_url = asset('storage/' . $product->image);
                }
                return $product;
            });
        } else {
            $products->transform(function ($product) {
                if ($product->image) {
                    $product->image_url = asset('storage/' . $product->image);
                }
                return $product;
            });
        }

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Display the specified product (public).
     */
    public function show($id)
    {
        $product = Product::where('is_active', true)
            ->with('seller:id,store_name,email')
            ->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Add image_url
        if ($product->image) {
            $product->image_url = asset('storage/' . $product->image);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Get available categories.
     */
    public function categories()
    {
        $categories = Product::where('is_active', true)
            ->select('category')
            ->distinct()
            ->pluck('category');

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}