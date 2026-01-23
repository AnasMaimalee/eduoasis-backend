<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBT Exam Started</title>
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
                               padding:32px 22px;">
                        <h1 style="margin:0; color:#ffffff; font-size:24px; font-weight:700;">
                            📝 CBT Exam Started
                        </h1>
                    </td>
                </tr>

                <!-- BODY -->
                <tr>
                    <td style="padding:38px 30px; color:#064e3b;">
                        <h2 style="margin:0 0 14px; font-size:22px; color:#064e3b;">
                            Hello {{ $user->name }},
                        </h2>

                        <p style="font-size:16px; line-height:1.7; margin:0 0 22px; color:#065f46;">
                            Your <strong>CBT exam</strong> has started successfully.
                            You can continue immediately.
                        </p>

                        <!-- EXAM INFO -->
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                               style="margin:24px 0;">
                            <tr>
                                <td style="padding:16px 18px; background:#f0fdf4;
                                           border-left:4px solid #10b981;
                                           border-radius:8px;">
                                    <p style="margin:0; font-size:16px;">
                                        <strong>Exam ID:</strong> {{ $examId }}
                                    </p>
                                    <p style="margin:6px 0 0; font-size:16px;">
                                        <strong>Status:</strong> Ongoing
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <p style="font-size:16px; line-height:1.7; margin:22px 0; color:#065f46;">
                            Click the button below to continue your exam.
                            Please complete it before the time expires.
                        </p>

                        <!-- CTA -->
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td align="center" style="padding:10px 0 28px;">
                                    <a href="{{ config('app.frontend_url') . '/cbt/exams/' . $examId }}"
                                       style="background:#10b981;
                                              color:#ffffff;
                                              text-decoration:none;
                                              padding:14px 34px;
                                              border-radius:10px;
                                              font-size:16px;
                                              font-weight:700;
                                              display:inline-block;">
                                        ▶ Continue Exam
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="font-size:14px; line-height:1.6; color:#047857;">
                            ⏱️ Note: Once the exam time elapses, your answers will be automatically submitted.
                        </p>
                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td align="center"
                        style="background:#ecfdf5; padding:20px;
                               font-size:12px; color:#065f46;">
                        &copy; {{ date('Y') }} JAMB Portal. All rights reserved.
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
