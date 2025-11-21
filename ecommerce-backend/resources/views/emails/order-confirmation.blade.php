<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .email-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .email-header p {
            font-size: 16px;
            opacity: 0.95;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }
        
        .email-body {
            padding: 40px 30px;
        }
        
        .order-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .order-info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .order-info-row:last-child {
            border-bottom: none;
        }
        
        .order-info-label {
            font-weight: 600;
            color: #666;
        }
        
        .order-info-value {
            color: #2c3e50;
            font-weight: 600;
        }
        
        .order-number {
            color: #667eea;
            font-size: 20px;
            font-weight: 700;
        }
        
        .section-title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .order-items {
            margin-bottom: 30px;
        }
        
        .order-item {
            display: flex;
            gap: 20px;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 15px;
            align-items: center;
        }
        
        .item-image {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .item-quantity {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .item-price {
            color: #667eea;
            font-size: 18px;
            font-weight: 700;
        }
        
        .shipping-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .shipping-row {
            margin-bottom: 10px;
            color: #666;
        }
        
        .shipping-row strong {
            color: #2c3e50;
            display: inline-block;
            min-width: 120px;
        }
        
        .order-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            color: #666;
        }
        
        .summary-divider {
            height: 1px;
            background: #e0e0e0;
            margin: 15px 0;
        }
        
        .summary-total {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .payment-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 10px;
        }
        
        .payment-cod {
            background: #e8f5e9;
            color: #4caf50;
        }
        
        .payment-stripe {
            background: #ede7f6;
            color: #6772e5;
        }
        
        .payment-paypal {
            background: #e3f2fd;
            color: #0070ba;
        }
        
        .email-footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        
        .email-footer p {
            margin-bottom: 10px;
        }
        
        .social-links {
            margin-top: 20px;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #667eea;
            text-decoration: none;
        }
        
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 20px;
        }
        
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 20px 15px;
            }
            
            .order-item {
                flex-direction: column;
            }
            
            .item-image {
                width: 100%;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="success-icon">✓</div>
            <h1>Order Confirmed!</h1>
            <p>Thank you for your order</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <!-- Order Info -->
            <div class="order-info">
                <div class="order-info-row">
                    <span class="order-info-label">Order Number:</span>
                    <span class="order-number">{{ $order->order_number }}</span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Order Date:</span>
                    <span class="order-info-value">{{ $order->created_at->format('F d, Y h:i A') }}</span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Payment Method:</span>
                    <span class="order-info-value">
                        @if($order->payment_method === 'cod')
                            <span class="payment-badge payment-cod">💵 Cash on Delivery</span>
                        @elseif($order->payment_method === 'stripe')
                            <span class="payment-badge payment-stripe">💳 Credit/Debit Card (Stripe)</span>
                        @elseif($order->payment_method === 'paypal')
                            <span class="payment-badge payment-paypal">💙 PayPal</span>
                        @endif
                    </span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Order Status:</span>
                    <span class="order-info-value" style="color: #ff9800; text-transform: capitalize;">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
            
            <!-- Order Items -->
            <h2 class="section-title">Order Items</h2>
            <div class="order-items">
                @foreach($order->items as $item)
                <div class="order-item">
                    <div class="item-image">
                        @if($item->product->image)
                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}">
                        @else
                            <img src="https://via.placeholder.com/100" alt="{{ $item->product->name }}">
                        @endif
                    </div>
                    <div class="item-details">
                        <div class="item-name">{{ $item->product->name }}</div>
                        <div class="item-quantity">Quantity: {{ $item->quantity }}</div>
                        <div class="item-price">₱{{ number_format($item->subtotal, 2) }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Shipping Information -->
            <h2 class="section-title">Shipping Information</h2>
            <div class="shipping-info">
                <div class="shipping-row">
                    <strong>Name:</strong> {{ $order->shipping_full_name }}
                </div>
                <div class="shipping-row">
                    <strong>Phone:</strong> {{ $order->shipping_phone }}
                </div>
                <div class="shipping-row">
                    <strong>Email:</strong> {{ $order->shipping_email }}
                </div>
                <div class="shipping-row">
                    <strong>Address:</strong> {{ $order->shipping_address }}
                </div>
                <div class="shipping-row">
                    <strong>City:</strong> {{ $order->shipping_city }}
                </div>
                <div class="shipping-row">
                    <strong>Province:</strong> {{ $order->shipping_province }}
                </div>
                <div class="shipping-row">
                    <strong>Postal Code:</strong> {{ $order->shipping_postal_code }}
                </div>
            </div>
            
            <!-- Order Summary -->
            <h2 class="section-title">Order Summary</h2>
            <div class="order-summary">
                <div class="summary-row">
                    <span>Subtotal ({{ $order->items->count() }} items):</span>
                    <span>₱{{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Shipping Fee:</span>
                    <span style="color: #4caf50; font-weight: 600;">FREE</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row summary-total">
                    <span>Total:</span>
                    <span>₱{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
            
            @if($order->order_notes)
            <!-- Order Notes -->
            <h2 class="section-title">Order Notes</h2>
            <div class="shipping-info">
                <p>{{ $order->order_notes }}</p>
            </div>
            @endif
            
            <!-- Call to Action -->
            <div style="text-align: center; margin-top: 30px;">
                <p style="color: #666; margin-bottom: 10px;">Track your order or view details:</p>
                <a href="{{ env('BUYER_FRONTEND_URL') }}" class="button">View My Orders</a>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p><strong>E-Commerce</strong></p>
            <p>Thank you for shopping with us!</p>
            <p>If you have any questions, please contact us at {{ env('MAIL_FROM_ADDRESS') }}</p>
            
            <div class="social-links">
                <a href="#">Facebook</a> | 
                <a href="#">Twitter</a> | 
                <a href="#">Instagram</a>
            </div>
            
            <p style="margin-top: 20px; font-size: 12px; color: #999;">
                © {{ date('Y') }} E-Commerce. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>