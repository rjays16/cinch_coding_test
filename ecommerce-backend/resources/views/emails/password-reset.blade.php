<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
        }
        .content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background: #42b983;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #999;
            font-size: 12px;
        }
        .footer a {
            color: #42b983;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>E-Commerce</h1>
        </div>
        
        <div class="content">
            <h2>Reset Your Password</h2>
            
            <p>Hello,</p>
            
            <p>We received a request to reset your password. Click the button below to reset it:</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetLink }}" class="button">Reset Password</a>
            </div>
            
            <p>Or copy and paste this link into your browser:</p>
            <p style="word-break: break-all; color: #666;">{{ $resetLink }}</p>
            
            <p><strong>This link will expire in 24 hours.</strong></p>
            
            <p>If you didn't request a password reset, you can safely ignore this email.</p>
            
            <p>Best regards,<br>E-Commerce Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} E-Commerce. All rights reserved.</p>
            <p>
                @if(isset($role) && $role === 'buyer')
                    <a href="{{ config('frontend.buyer_url') }}">Visit Our Store</a>
                @else
                    <a href="{{ config('frontend.seller_url') }}">Visit Seller Dashboard</a>
                @endif
            </p>
        </div>
    </div>
</body>
</html>