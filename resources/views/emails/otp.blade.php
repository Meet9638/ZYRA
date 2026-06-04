<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .otp-code {
            background-color: #f8f9fa;
            border: 2px dashed #2563eb;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 8px;
        }
        .otp-number {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
            letter-spacing: 5px;
            margin: 10px 0;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">ZYRA</div>
            <h1>OTP Verification</h1>
        </div>

        @switch($type)
            @case('login')
                <p>Hello {{ $user->name }},</p>
                <p>You requested to log in to your ZYRA account. Please use the following One-Time Password (OTP) to complete the verification process:</p>
                @break

            @case('password_reset')
                <p>Hello {{ $user->name }},</p>
                <p>You requested to reset your password. Please use the following One-Time Password (OTP) to proceed:</p>
                @break

            @case('email_verification')
                <p>Hello {{ $user->name }},</p>
                <p>Please use the following One-Time Password (OTP) to verify your email address:</p>
                @break

            @default
                <p>Hello {{ $user->name }},</p>
                <p>Please use the following One-Time Password (OTP) to complete your request:</p>
        @endswitch

        <div class="otp-code">
            <p>Your OTP code is:</p>
            <div class="otp-number">{{ $otp->code }}</div>
        </div>

        <div class="warning">
            <strong>⚠️ Security Notice:</strong><br>
            • This OTP will expire in 10 minutes<br>
            • Never share this code with anyone<br>
            • ZYRA will never ask for your OTP via phone<br>
            • If you didn't request this, please ignore this email
        </div>

        @if($type === 'login')
            <div style="text-align: center;">
                <a href="{{ route('otp.verify.form') }}" class="button">Verify OTP Now</a>
            </div>
        @endif

        <div class="footer">
            <p>This is an automated message from ZYRA.</p>
            <p>If you have any questions, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} ZYRA. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

