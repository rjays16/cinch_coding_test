<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Services\StripeService;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Create a new order
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping.fullName' => 'required|string',
            'shipping.phone' => 'required|string',
            'shipping.email' => 'required|email',
            'shipping.address' => 'required|string',
            'shipping.city' => 'required|string',
            'shipping.province' => 'required|string',
            'shipping.postalCode' => 'required|string',
            'payment_method' => 'required|in:cod,stripe,paypal',
            'order_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::guard('buyer')->user();

            // Get cart items
            $cartItems = Cart::where('user_id', $user->id)
                ->with('product')
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            // CHECK STOCK AVAILABILITY FIRST
            foreach ($cartItems as $item) {
                if ($item->product->stock < $item->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for {$item->product->name}. Only {$item->product->stock} available."
                    ], 400);
                }
            }

            // Calculate totals
            $subtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });
            $shippingFee = 0; // Free shipping
            $total = $subtotal + $shippingFee;

            // Generate order number
            $orderNumber = 'ORD-' . strtoupper(uniqid());

            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'shipping_full_name' => $request->shipping['fullName'],
                'shipping_phone' => $request->shipping['phone'],
                'shipping_email' => $request->shipping['email'],
                'shipping_address' => $request->shipping['address'],
                'shipping_city' => $request->shipping['city'],
                'shipping_province' => $request->shipping['province'],
                'shipping_postal_code' => $request->shipping['postalCode'],
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cod' ? 'pending' : 'pending',
                'status' => 'pending',
                'order_notes' => $request->order_notes,
            ]);

            // Create order items AND reduce stock
            foreach ($cartItems as $cartItem) {
                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                    'subtotal' => $cartItem->quantity * $cartItem->product->price,
                ]);

                // REDUCE STOCK
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // Handle Stripe payment
            if ($request->payment_method === 'stripe') {
                $orderData = [
                    'order_number' => $orderNumber,
                    'user_id' => $user->id,
                    'shipping' => $request->shipping,
                    'items' => $cartItems->map(function ($item) {
                        return [
                            'product' => [
                                'name' => $item->product->name,
                                'price' => $item->product->price,
                                'category' => $item->product->category,
                                'image_url' => $item->product->image ? asset('storage/' . $item->product->image) : null,
                            ],
                            'quantity' => $item->quantity,
                        ];
                    })->toArray(),
                ];

                $stripeSession = $this->stripeService->createCheckoutSession($orderData);

                if (!$stripeSession['success']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to create Stripe session',
                        'error' => $stripeSession['message']
                    ], 500);
                }

                // Save Stripe session ID
                $order->update([
                    'stripe_session_id' => $stripeSession['session_id']
                ]);

                DB::commit();

                // Clear cart
                Cart::where('user_id', $user->id)->delete();

                // Send order confirmation email for Stripe
                try {
                    // Reload order with relationships for email
                    $order = Order::with('items.product')->find($order->id);
                    Mail::to($order->shipping_email)->send(new OrderConfirmation($order));
                    
                } catch (\Exception $e) {
                    \Log::error("Failed to send order confirmation email: " . $e->getMessage());
                    // Continue even if email fails - don't break the order process
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Order created successfully',
                    'data' => [
                        'order' => $order,
                        'stripe_url' => $stripeSession['url'],
                        'requires_payment' => true,
                    ]
                ], 201);
            }

            // For COD and PayPal
            DB::commit();

            // Clear cart
            Cart::where('user_id', $user->id)->delete();

            // Send order confirmation email for COD/PayPal
            try {
                // Reload order with relationships for email
                $order = Order::with('items.product')->find($order->id);
                
                Mail::to($order->shipping_email)->send(new OrderConfirmation($order));
            } catch (\Exception $e) {
                \Log::error("Failed to send order confirmation email: " . $e->getMessage());
                // Continue even if email fails - don't break the order process
            }

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order' => $order,
                    'requires_payment' => false,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user orders
     */
    public function index()
    {
        try {
            $user = Auth::guard('buyer')->user();

            $orders = Order::where('user_id', $user->id)
                ->with('items.product')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single order
     */
    public function show($id)
    {
        try {
            $user = Auth::guard('buyer')->user();

            $order = Order::where('user_id', $user->id)
                ->where('id', $id)
                ->with('items.product')
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }

    /**
     * Verify Stripe payment
     */
    public function verifyStripePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->stripeService->verifyPayment($request->session_id);

            if ($result['success'] && $result['status'] === 'paid') {
                // Update order payment status
                $order = Order::where('stripe_session_id', $request->session_id)->first();
                
                if ($order) {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified successfully',
                    'data' => $result
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment verification error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel order and restore stock
     */
    public function cancel($id)
    {
        try {
            $user = Auth::guard('buyer')->user();

            $order = Order::where('user_id', $user->id)
                ->where('id', $id)
                ->with('items.product')
                ->firstOrFail();

            // Only allow cancellation of pending orders
            if ($order->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending orders can be cancelled'
                ], 400);
            }

            DB::beginTransaction();

            // Restore stock for each item
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            // Update order status
            $order->update([
                'status' => 'cancelled'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled and stock restored'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order'
            ], 500);
        }
    }
}