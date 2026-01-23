<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wallet Credited</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">

<!-- OUTER WRAPPER -->
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="padding:32px 12px;">
    <tr>
        <td align="center">

            <!-- EMAIL CONTAINER -->
            <table width="600" cellpadding="0" cellspacing="0" role="presentation"
                   style="width:100%; max-width:600px; background:#ffffff; border-radius:10px;
                   overflow:hidden; box-shadow:0 6px 18px rgba(0,0,0,0.08);">

                <!-- HEADER -->
                <tr>
                    <td align="center"
                        style="background-color:#10b981;
                               background-image:linear-gradient(135deg,#10b981,#047857);
                               padding:28px 20px; color:#ffffff;">
                        <h2 style="margin:0; font-size:22px; font-weight:600;">
                            💳 Wallet Credited
                        </h2>
                        <p style="margin:8px 0 0; font-size:13px; opacity:0.95;">
                            Your transaction was successful
                        </p>
                    </td>
                </tr>

                <!-- BODY -->
                <tr>
                    <td style="padding:36px 28px; color:#1f2937;">
                        <p style="font-size:16px; margin:0 0 14px;">
                            Hello <strong>{{ $user->name }}</strong>,
                        </p>

                        <p style="font-size:15px; line-height:1.7; margin:0 0 24px;">
                            We’re happy to let you know that your wallet has been
                            <strong style="color:#10b981;">successfully credited</strong>.
                            Here are the details of your transaction:
                        </p>

                        <!-- TRANSACTION DETAILS -->
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                               style="border-collapse:separate; border-spacing:0 12px;">

                            <tr>
                                <td style="padding:14px 16px; background:#f0fdf4; font-weight:600;
                                           border-radius:10px 0 0 10px;">
                                    Amount Credited
                                </td>
                                <td style="padding:14px 16px; background:#f0fdf4; text-align:right;
                                           border-radius:0 10px 10px 0; font-weight:600;">
                                    ₦{{ number_format($amount, 2) }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:14px 16px; background:#f0fdf4; font-weight:600;
                                           border-radius:10px 0 0 10px;">
                                    New Wallet Balance
                                </td>
                                <td style="padding:14px 16px; background:#f0fdf4; text-align:right;
                                           border-radius:0 10px 10px 0; font-weight:600;">
                                    ₦{{ number_format($balance, 2) }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:14px 16px; background:#f0fdf4; font-weight:600;
                                           border-radius:10px 0 0 10px;">
                                    Reason
                                </td>
                                <td style="padding:14px 16px; background:#f0fdf4; text-align:right;
                                           border-radius:0 10px 10px 0;">
                                    {{ $reason }}
                                </td>
                            </tr>
                        </table>

                        <p style="font-size:15px; line-height:1.7; margin:26px 0 0;">
                            You can now continue using our services without any interruption.
                        </p>

                        <p style="margin:34px 0 0;">
                            Warm regards,<br>
                            <strong style="color:#10b981;">{{ config('app.name') }} Team</strong>
                        </p>
                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td align="center"
                        style="background:#f0fdf4; padding:18px 20px;
                               font-size:12px; color:#065f46; line-height:1.6;">
                        If you did not authorize this transaction, please contact our support team immediately.
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
