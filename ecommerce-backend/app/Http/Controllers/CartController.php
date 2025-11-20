<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Get user's cart
     */
    public function index()
{
    try {
        $user = Auth::guard('buyer')->user();

        $cartItems = Cart::where('user_id', $user->id)
            ->with('product', 'product.seller')
            ->get()
            ->map(function($item) {
                if (!$item->product) {
                    return null;
                }
                
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'price' => (float) $item->product->price,
                        'image_url' => $item->product->image ? asset('storage/' . $item->product->image) : null,
                        'stock' => $item->product->stock,
                        'seller' => $item->product->seller ? $item->product->seller->store_name : 'Unknown Seller',
                        'category' => $item->product->category ?? 'General',
                        'brand' => $item->product->brand ?? '',
                    ],
                    'subtotal' => $item->quantity * $item->product->price
                ];
            })
            ->filter()
            ->values();

        // Calculate totals
        $count = $cartItems->sum('quantity');
        $total = $cartItems->sum('subtotal');

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'total' => (float) $total,
                'count' => (int) $count
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Cart fetch error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch cart',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'integer|min:1'
            ]);

            $user = Auth::guard('buyer')->user();
            $product = Product::findOrFail($request->product_id);

            // Check stock
            if ($product->stock < ($request->quantity ?? 1)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock'
                ], 400);
            }

            // Check if item already in cart
            $cartItem = Cart::where('user_id', $user->id)
                           ->where('product_id', $request->product_id)
                           ->first();

            if ($cartItem) {
                // Update quantity
                $newQuantity = $cartItem->quantity + ($request->quantity ?? 1);
                
                if ($product->stock < $newQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock'
                    ], 400);
                }

                $cartItem->quantity = $newQuantity;
                $cartItem->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Cart updated successfully',
                    'data' => $cartItem
                ]);
            } else {
                // Create new cart item
                $cartItem = Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity ?? 1
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Item added to cart',
                    'data' => $cartItem
                ], 201);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $user = Auth::guard('buyer')->user();
            $cartItem = Cart::where('user_id', $user->id)
                           ->where('id', $id)
                           ->firstOrFail();

            $product = Product::findOrFail($cartItem->product_id);

            // Check stock
            if ($product->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock'
                ], 400);
            }

            $cartItem->quantity = $request->quantity;
            $cartItem->save();

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'data' => $cartItem
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function destroy($id)
    {
        try {
            $user = Auth::guard('buyer')->user();
            $cartItem = Cart::where('user_id', $user->id)
                           ->where('id', $id)
                           ->firstOrFail();

            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        try {
            $user = Auth::guard('buyer')->user();
            Cart::where('user_id', $user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cart count
     */
    public function count()
    {
        try {
            $user = Auth::guard('buyer')->user();
            $count = Cart::where('user_id', $user->id)->sum('quantity');

            return response()->json([
                'success' => true,
                'data' => [
                    'count' => $count
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get cart count',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}