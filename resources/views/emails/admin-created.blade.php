<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Your Administrator Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0; padding:0; background-color:#ecfdf5; font-family:Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="padding:32px 12px;">
    <tr>
        <td align="center">

            <!-- CONTAINER -->
            <table width="600" cellpadding="0" cellspacing="0" role="presentation"
                   style="width:100%; max-width:600px; background:#ffffff;
                   border-radius:14px; overflow:hidden;
                   box-shadow:0 12px 28px rgba(16,185,129,0.18);">

                <!-- HEADER -->
                <tr>
                    <td align="center"
                        style="background-color:#10b981;
                               background-image:linear-gradient(135deg,#10b981,#047857);
                               padding:34px 24px;">
                        <h1 style="margin:0; color:#ffffff; font-size:26px; font-weight:800;">
                            EduOasis Admin Portal
                        </h1>
                        <p style="margin:8px 0 0; color:#d1fae5; font-size:14px;">
                            Secure Administrator Access
                        </p>
                    </td>
                </tr>

                <!-- BODY -->
                <tr>
                    <td style="padding:38px 30px; color:#064e3b;">
                        <h2 style="margin:0 0 14px; font-size:22px; color:#064e3b;">
                            Hello {{ $user->name }},
                        </h2>

                        <p style="font-size:16px; line-height:1.7; margin:0 0 16px; color:#065f46;">
                            Your administrator account has been created successfully on
                            <strong>EduOasis</strong>.
                        </p>

                        <p style="font-size:16px; line-height:1.7; margin:0 0 28px; color:#065f46;">
                            For security reasons, you are required to set your own password before accessing the system.
                        </p>

                        <!-- CTA BUTTON -->
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td align="center" style="padding:10px 0 32px;">
                                    <a href="{{ url('/set-password?token='.$token.'&email='.$user->email) }}"
                                       style="background:#10b981;
                                              color:#ffffff;
                                              text-decoration:none;
                                              padding:15px 36px;
                                              border-radius:10px;
                                              font-size:16px;
                                              font-weight:700;
                                              display:inline-block;">
                                        🔐 Set Your Password
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="font-size:14px; line-height:1.6; color:#047857; margin:0 0 22px;">
                            This link is secure and will expire automatically.
                            If you did not expect this email, you can safely ignore it.
                        </p>

                        <p style="font-size:14px; color:#047857; margin:0;">
                            Welcome aboard,<br>
                            <strong>EduOasis Team</strong>
                        </p>
                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td align="center"
                        style="background:#ecfdf5; padding:20px; font-size:12px; color:#065f46;">
                        <p style="margin:0;">
                            © {{ date('Y') }} EduOasis. All rights reserved.
                        </p>
                        <p style="margin:6px 0 0; color:#047857;">
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
