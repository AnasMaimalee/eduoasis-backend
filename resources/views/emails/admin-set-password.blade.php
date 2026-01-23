<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Your Administrator Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0; padding:0; background-color:#ecfdf5; font-family: Arial, Helvetica, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding: 30px 0;">
    <tr>
        <td align="center">

            <!-- Container -->
            <table width="600" cellpadding="0" cellspacing="0"
                   style="background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 10px 30px rgba(16,185,129,0.15);">

                <!-- Header -->
                <tr>
                    <td style="background:linear-gradient(135deg,#10b981,#059669); padding:32px; text-align:center;">
                        <h1 style="margin:0; color:#ffffff; font-size:26px; font-weight:800;">
                            EduOasis Admin Portal
                        </h1>
                        <p style="margin:8px 0 0; color:#d1fae5; font-size:14px;">
                            Secure Administrator Access
                        </p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:36px;">
                        <h2 style="margin-top:0; color:#064e3b; font-size:22px;">
                            Hello {{ $user->name }},
                        </h2>

                        <p style="color:#065f46; font-size:16px; line-height:1.7;">
                            Your administrator account has been created successfully on
                            <strong>EduOasis</strong>.
                        </p>

                        <p style="color:#065f46; font-size:16px; line-height:1.7;">
                            For security reasons, you are required to set your own password before accessing the system.
                        </p>

                        <!-- Button -->
                        <p style="text-align:center; margin:30px 0;">
                            <a href="{{ $resetUrl }}"
                            style="
                                background-color:#10b981;
                                color:#ffffff;
                                text-decoration:none;
                                padding:14px 32px;
                                border-radius:10px;
                                font-size:16px;
                                font-weight:bold;
                                display:inline-block;
                            ">
                                🔐 Set Your Password
                            </a>
                        </p>


                        <p style="color:#047857; font-size:14px; line-height:1.6;">
                            This link is secure and will expire automatically.
                            If you did not expect this email, you can safely ignore it.
                        </p>

                        <p style="color:#047857; font-size:14px; margin-top:24px;">
                            Welcome aboard,<br>
                            <strong>EduOasis Team</strong>
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background:#ecfdf5; padding:20px; text-align:center;">
                        <p style="margin:0; font-size:12px; color:#065f46;">
                            © {{ date('Y') }} EduOasis. All rights reserved.
                        </p>
                        <p style="margin:6px 0 0; font-size:12px; color:#047857;">
                            This is an automated message — please do not reply.
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>
</body>
</html>
