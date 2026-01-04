<!DOCTYPE html>
<html lang="en" style="font-family: Arial, sans-serif; background-color: #f4f4f7; padding: 0; margin: 0;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Account Created</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f7;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f7; padding: 20px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <!-- Header -->
                <tr>
                    <td align="center" style="background-color: #0d6efd; padding: 30px 0;">
                        <h1 style="color: #ffffff; font-size: 24px; margin: 0;">JAMB Portal</h1>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding: 30px;">
                        <h2 style="color: #333333; font-size: 22px; margin-top: 0;">Hello {{ $user->name }},</h2>
                        <p style="color: #555555; font-size: 16px; line-height: 1.6;">
                            Your administrator account has been successfully created on <strong>JAMB Portal</strong>.
                        </p>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
                            <tr>
                                <td style="padding: 10px; background-color: #f0f0f0; border-radius: 4px;">
                                    <p style="margin: 0; font-size: 16px;"><strong>Email:</strong> {{ $user->email }}</p>
                                    <p style="margin: 0; font-size: 16px;"><strong>Password:</strong> {{ $password }}</p>
                                </td>
                            </tr>
                        </table>

                        <p style="color: #555555; font-size: 16px; line-height: 1.6;">
                            Please login using the button below and change your password immediately for security purposes.
                        </p>

                        <p style="text-align: center; margin: 30px 0;">
                            <a href="{{ url('/login') }}" style="background-color: #0d6efd; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 5px; font-size: 16px; display: inline-block;">
                                Login Now
                            </a>
                        </p>

                        <p style="color: #999999; font-size: 14px; line-height: 1.5;">
                            If you did not expect this email, please ignore it.
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="background-color: #f4f4f7; padding: 20px;">
                        <p style="color: #999999; font-size: 12px; margin: 0;">
                            &copy; {{ date('Y') }} JAMB Portal. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
