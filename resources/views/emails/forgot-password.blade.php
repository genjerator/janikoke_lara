<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Your New Password</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 520px; margin: 40px auto; background: #fff; border-radius: 8px; padding: 36px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        h2 { color: #333; }
        .password-box { background: #f0f4ff; border: 1px solid #c7d7f9; border-radius: 6px; padding: 14px 20px; font-size: 22px; font-weight: bold; letter-spacing: 2px; color: #1a3a8f; margin: 24px 0; text-align: center; }
        p { color: #555; line-height: 1.6; }
        .footer { margin-top: 32px; font-size: 12px; color: #aaa; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hi {{ $userName }},</h2>
        <p>We received a request to reset your password. Your new temporary password is:</p>
        <div class="password-box">{{ $newPassword }}</div>
        <p>Please log in using this password and change it immediately in your account settings.</p>
        <p>If you did not request a password reset, please ignore this email or contact support.</p>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>

