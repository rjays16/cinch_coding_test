<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of products for the authenticated seller.
     */
    public function index(Request $request)
    {
        $query = Product::where('seller_id', $request->user()->id);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $isActive = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
            $query->where('is_active', $isActive);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSortFields = ['name', 'price', 'stock', 'created_at', 'updated_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        if ($request->has('per_page') && $request->per_page === 'all') {
            $products = $query->get();
        } else {
            $perPage = $request->get('per_page', 10);
            $products = $query->paginate($perPage);
        }

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        // Decode sizes if it's a JSON string from FormData
        if ($request->has('sizes') && is_string($request->sizes)) {
            $decodedSizes = json_decode($request->sizes, true);
            $request->merge(['sizes' => $decodedSizes]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku',
            'category' => 'required|string',
            'brand' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'sizes' => 'nullable|array',
            'color' => 'nullable|string',
            'weight' => 'nullable|numeric|min:0',
            'material' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('image');
        $data['seller_id'] = $request->user()->id;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified product.
     */
    public function show(Request $request, $id)
    {
        $product = Product::where('seller_id', $request->user()->id)->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, $id)
    {
        $product = Product::where('seller_id', $request->user()->id)->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or unauthorized'
            ], 404);
        }

        // Decode sizes if it's a JSON string from FormData
        if ($request->has('sizes') && is_string($request->sizes)) {
            $decodedSizes = json_decode($request->sizes, true);
            $request->merge(['sizes' => $decodedSizes]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'sometimes|required|string|unique:products,sku,' . $id,
            'category' => 'sometimes|required|string',
            'brand' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'is_active' => 'boolean',
            'sizes' => 'nullable|array',
            'color' => 'nullable|string',
            'weight' => 'nullable|numeric|min:0',
            'material' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('image');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->name ?? $product->name) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->fresh()
        ]);
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Request $request, $id)
    {
        $product = Product::where('seller_id', $request->user()->id)->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or unauthorized'
            ], 404);
        }

        // Delete image
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * Get product statistics for the authenticated seller.
     */
    public function stats(Request $request)
    {
        $sellerId = $request->user()->id;

        $totalProducts = Product::where('seller_id', $sellerId)->count();
        $activeProducts = Product::where('seller_id', $sellerId)->where('is_active', true)->count();
        $inactiveProducts = Product::where('seller_id', $sellerId)->where('is_active', false)->count();
        $lowStockProducts = Product::where('seller_id', $sellerId)->where('stock', '<', 10)->where('stock', '>', 0)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_products' => $totalProducts,
                'active_products' => $activeProducts,
                'inactive_products' => $inactiveProducts,
                'low_stock_products' => $lowStockProducts,
            ]
        ]);
    }
}