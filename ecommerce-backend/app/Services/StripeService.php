<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Exception;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create Stripe Checkout Session
     */
    public function createCheckoutSession($orderData)
    {
        try {
            $lineItems = [];

            // Convert cart items to Stripe line items
            foreach ($orderData['items'] as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'php',
                        'product_data' => [
                            'name' => $item['product']['name'],
                            'description' => $item['product']['category'] ?? '',
                            'images' => [$item['product']['image_url'] ?? ''],
                        ],
                        'unit_amount' => (int)($item['product']['price'] * 100), // Convert to cents
                    ],
                    'quantity' => $item['quantity'],
                ];
            }

            // Create Stripe Checkout Session
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => config('app.frontend_url') . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('app.frontend_url') . '/checkout/cancel',
                'customer_email' => $orderData['shipping']['email'],
                'metadata' => [
                    'order_number' => $orderData['order_number'],
                    'user_id' => $orderData['user_id'],
                ],
            ]);

            return [
                'success' => true,
                'session_id' => $session->id,
                'url' => $session->url,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify Stripe Payment
     */
    public function verifyPayment($sessionId)
    {
        try {
            $session = Session::retrieve($sessionId);

            return [
                'success' => true,
                'status' => $session->payment_status,
                'amount' => $session->amount_total / 100,
                'metadata' => $session->metadata,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}