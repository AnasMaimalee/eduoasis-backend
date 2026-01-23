<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Rejected</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0; padding:0; background-color:#ecfdf5; font-family: Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:32px 12px;">
    <tr>
        <td align="center">

            <!-- CONTAINER -->
            <table width="600" cellpadding="0" cellspacing="0"
                   style="width:100%; max-width:600px; background:#ffffff;
                          border-radius:14px; overflow:hidden;
                          box-shadow:0 10px 26px rgba(16,185,129,0.15);">

                <!-- HEADER -->
                <tr>
                    <td align="center"
                        style="background-color:#dc2626; padding:30px 22px; border-radius:14px 14px 0 0;">
                        <h1 style="margin:0; color:#ffffff; font-size:24px; font-weight:700;">
                            ❌ Service Rejected
                        </h1>
                        <p style="margin:8px 0 0; color:#fee2e2; font-size:14px;">
                            Your request could not be approved
                        </p>
                    </td>
                </tr>

                <!-- BODY -->
                <tr>
                    <td style="padding:36px 30px; color:#064e3b;">

                        <p style="font-size:16px; line-height:1.7; margin:0 0 18px;">
                            Hello <strong>{{ $job->user->name }}</strong>,
                        </p>

                        <p style="font-size:16px; line-height:1.7; margin:0 0 22px;">
                            Unfortunately, your request for the service
                            <strong>{{ $job->service->name }}</strong> has been
                            <strong style="color:#dc2626;">rejected</strong>.
                        </p>

                        <p style="font-weight:600; margin:0 0 6px;">Reason:</p>
                        <p style="background:#fee2e2; padding:12px; border-left:4px solid #dc2626; border-radius:6px; margin:0 0 22px;">
                            {{ $job->rejection_reason }}
                        </p>

                        <p style="font-size:16px; line-height:1.7; margin:0 0 24px;">
                            The amount of <strong>₦{{ number_format($job->customer_price, 2) }}</strong>
                            has been fully refunded to your wallet.
                        </p>

                        <p style="font-size:14px; line-height:1.6; color:#047857; margin-bottom:24px;">
                            If you have any questions, please contact support.
                        </p>

                        <p style="font-size:14px; color:#047857;">
                            Regards,<br>
                            <strong>{{ config('app.name') }} Team</strong>
                        </p>

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td align="center"
                        style="background:#ecfdf5; padding:20px;
                               font-size:12px; color:#065f46;">
                        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
