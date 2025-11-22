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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Header with Background Image */
        .email-header {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1600&h=600&fit=crop') center/cover;
            color: white;
            padding: 50px 30px;
            text-align: center;
        }

        .email-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .email-header p {
            font-size: 16px;
            opacity: 0.95;
        }

        .email-body {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }

        .message {
            font-size: 15px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.8;
        }

        /* Order Details */
        .order-details {
            background: #f9fafb;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .order-details h2 {
            font-size: 20px;
            color: #1f2937;
            margin-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #4b5563;
            font-size: 14px;
            min-width: 120px;
            flex-shrink: 0;
        }

        .detail-value {
            color: #1f2937;
            font-size: 14px;
            font-weight: 500;
            text-align: right;
            word-break: break-word;
            flex: 1;
        }

        /* Order Items */
        .order-items {
            margin-bottom: 30px;
        }

        .order-items h2 {
            font-size: 20px;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .item {
            display: flex;
            gap: 15px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 6px;
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
            font-weight: 600;
            color: #1f2937;
            font-size: 15px;
            margin-bottom: 5px;
        }

        .item-meta {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .item-price {
            font-weight: 600;
            color: #667eea;
            font-size: 15px;
        }

        /* Order Summary */
        .order-summary {
            background: #f9fafb;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 15px;
        }

        .summary-row.total {
            border-top: 2px solid #e5e7eb;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }

        /* CTA Button */
        .cta-button {
            text-align: center;
            margin: 30px 0;
        }

        .cta-button a {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
        }

        /* Footer */
        .email-footer {
            background: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .email-footer p {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 15px;
        }

        .social-links {
            margin: 20px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }

        .contact-info {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 15px;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 30px 20px;
            }

            .email-header {
                padding: 40px 20px;
            }

            .email-header h1 {
                font-size: 26px;
            }

            .order-details,
            .order-summary {
                padding: 20px;
            }

            .detail-row {
                flex-direction: column;
                gap: 5px;
            }

            .detail-value {
                text-align: left;
            }

            .item {
                flex-direction: column;
            }

            .item-image {
                width: 100%;
                height: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header with Background Image -->
        <div class="email-header">
            <h1>🎉 Order Confirmed!</h1>
            <p>Thank you for your purchase</p>
        </div>

        <!-- Email Body -->
        <div class="email-body">
            <p class="greeting">Hi {{ $order->user->first_name }},</p>
            
            <p class="message">
                Great news! Your order has been confirmed and is being processed. 
                We'll send you another email when your order has been shipped.
            </p>

            <!-- Order Details -->
            <div class="order-details">
                <h2>Order Details</h2>
                
                <div class="detail-row">
                    <span class="detail-label">Order Number:</span>
                    <span class="detail-value">{{ $order->order_number }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Order Date:</span>
                    <span class="detail-value">{{ $order->created_at->format('F d, Y') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Payment Method:</span>
                    <span class="detail-value">{{ strtoupper($order->payment_method) }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Payment Status:</span>
                    <span class="detail-value">{{ ucfirst($order->payment_status) }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Order Status:</span>
                    <span class="detail-value">{{ ucfirst($order->status) }}</span>
                </div>
            </div>

            <!-- Order Items -->
            <div class="order-items">
                <h2>Order Items</h2>
                
                @foreach($order->items as $item)
                <div class="item">
                    <div class="item-image">
                        @php
                            if($item->product->image && !app()->environment('local')) {
                                $imageUrl = url('storage/' . $item->product->image);
                            } else {
                                $initials = strtoupper(substr($item->product->name, 0, 2));
                                $imageUrl = 'https://via.placeholder.com/100/667eea/ffffff?text=' . urlencode($initials);
                            }
                        @endphp
                        <img src="{{ $imageUrl }}" alt="{{ $item->product->name }}">
                    </div>
                    <div class="item-details">
                        <div class="item-name">{{ $item->product->name }}</div>
                        <div class="item-meta">Quantity: {{ $item->quantity }} × ₱{{ number_format($item->price, 2) }}</div>
                        <div class="item-price">₱{{ number_format($item->subtotal, 2) }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>₱{{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Shipping Fee:</span>
                    <span>
                        @if($order->shipping_fee > 0)
                            ₱{{ number_format($order->shipping_fee, 2) }}
                        @else
                            <span style="color: #10b981; font-weight: 600;">FREE</span>
                        @endif
                    </span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span>₱{{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="order-details">
                <h2>Shipping Information</h2>
                
                <div class="detail-row">
                    <span class="detail-label">Full Name:</span>
                    <span class="detail-value">{{ $order->shipping_full_name ?: $order->user->first_name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $order->shipping_email ?: $order->user->email }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">{{ $order->shipping_phone }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Address:</span>
                    <span class="detail-value">
                        {{ $order->shipping_address }}
                        @if($order->shipping_city || $order->shipping_province || $order->shipping_postal_code)
                            <br>
                            @if($order->shipping_city){{ $order->shipping_city }}@endif
                            @if($order->shipping_province), {{ $order->shipping_province }}@endif
                            @if($order->shipping_postal_code) {{ $order->shipping_postal_code }}@endif
                        @endif
                    </span>
                </div>
                
                @if($order->order_notes)
                <div class="detail-row">
                    <span class="detail-label">Order Notes:</span>
                    <span class="detail-value">{{ $order->order_notes }}</span>
                </div>
                @endif
            </div>

            <!-- CTA Button -->
            <div class="cta-button">
                <a href="{{ config('app.frontend_url') }}/orders/{{ $order->id }}">View My Orders</a>
            </div>

            <p class="message" style="text-align: center; font-size: 14px;">
                If you have any questions, feel free to contact our support team.
            </p>
        </div>

        <!-- Footer -->
        @php
            $appName = config('app.name', 'E-Commerce');
            $supportEmail = 'support@' . strtolower(str_replace(' ', '', $appName)) . '.com';
        @endphp

        <div class="email-footer">
            <p style="font-weight: 600; color: #1f2937;">Thank you for shopping with us!</p>
            
            <div class="social-links">
                <a href="#">Facebook</a> • 
                <a href="#">Twitter</a> • 
                <a href="#">Instagram</a>
            </div>

            <div class="contact-info">
                <p style="margin-bottom: 5px;">{{ $appName }}</p>
                <p style="margin-bottom: 5px;">
                    <a href="mailto:{{ $supportEmail }}" 
                    style="color: #667eea; text-decoration: none;">
                        {{ $supportEmail }}
                    </a>
                </p>
                <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            </div>
        </div>
</body>
</html>