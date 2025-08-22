<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset Request</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 50px auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h1 style="color: #333333;">Hello {{ $name }},</h1>
        <p style="color: #555555; line-height: 1.5;">
            We received a request to reset your password. To proceed with resetting your password, please click the button below:
        </p>
        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}" style="background-color: #3490dc; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block;">
                Reset Password
            </a>
        </p>
        <p style="color: #555555; line-height: 1.5;">
            If you did not request a password reset, please ignore this email.
        </p>
        <p style="color: #555555; line-height: 1.5;">
            Best regards,<br>
            {{ config('app.name') }} Team
        </p>
    </div>
</body>
</html>
