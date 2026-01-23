<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admission Letter Ready</title>
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
                   box-shadow:0 10px 26px rgba(16,185,129,0.15);">

                <!-- HEADER -->
                <tr>
                    <td align="center"
                        style="background-color:#10b981;
                               background-image:linear-gradient(135deg,#10b981,#047857);
                               padding:30px 22px;">
                        <h1 style="margin:0; color:#ffffff; font-size:24px; font-weight:700;">
                            🎓 Admission Letter Ready
                        </h1>
                        <p style="margin:8px 0 0; color:#d1fae5; font-size:14px;">
                            Your request has been completed
                        </p>
                    </td>
                </tr>

                <!-- BODY -->
                <tr>
                    <td style="padding:36px 30px; color:#064e3b;">

                        <p style="font-size:16px; line-height:1.7; margin:0 0 18px;">
                            Hello <strong>{{ $job->user->name }}</strong>,
                        </p>

                        <p style="font-size:16px; line-height:1.7; margin:0 0 22px; color:#065f46;">
                            Your <strong>JAMB Admission Letter</strong> request has been
                            <strong style="color:#10b981;">successfully completed</strong>.
                        </p>

                        <!-- DETAILS -->
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                               style="margin:24px 0; border-collapse:separate; border-spacing:0 10px;">

                            <tr>
                                <td style="padding:14px 16px; background:#f0fdf4; font-weight:600;
                                           border-radius:10px 0 0 10px;">
                                    Profile Code
                                </td>
                                <td style="padding:14px 16px; background:#f0fdf4;
                                           border-radius:0 10px 10px 0;">
                                    {{ $job->profile_code }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:14px 16px; background:#f0fdf4; font-weight:600;
                                           border-radius:10px 0 0 10px;">
                                    Registration Number
                                </td>
                                <td style="padding:14px 16px; background:#f0fdf4;
                                           border-radius:0 10px 10px 0;">
                                    {{ $job->registration_number ?? 'N/A' }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:14px 16px; background:#f0fdf4; font-weight:600;
                                           border-radius:10px 0 0 10px;">
                                    Service
                                </td>
                                <td style="padding:14px 16px; background:#f0fdf4;
                                           border-radius:0 10px 10px 0;">
                                    {{ $job->service->name }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:14px 16px; background:#f0fdf4; font-weight:600;
                                           border-radius:10px 0 0 10px;">
                                    Status
                                </td>
                                <td style="padding:14px 16px; background:#f0fdf4;
                                           border-radius:0 10px 10px 0;">
                                    Completed (Awaiting Approval)
                                </td>
                            </tr>

                        </table>

                        <p style="font-size:16px; line-height:1.7; margin:22px 0;">
                            You can download your admission letter using the button below:
                        </p>

                        <!-- CTA -->
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td align="center" style="padding:10px 0 28px;">
                                    <a href="{{ asset('storage/' . $job->result_file) }}"
                                       style="background:#10b981;
                                              color:#ffffff;
                                              text-decoration:none;
                                              padding:14px 36px;
                                              border-radius:10px;
                                              font-size:16px;
                                              font-weight:700;
                                              display:inline-block;">
                                        ⬇ Download Admission Letter
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="font-size:14px; line-height:1.6; color:#047857;">
                            If you have any questions, feel free to contact support.
                        </p>

                        <p style="font-size:14px; color:#047857; margin-top:24px;">
                            Regards,<br>
                            <strong>{{ config('app.name') }}</strong>
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
