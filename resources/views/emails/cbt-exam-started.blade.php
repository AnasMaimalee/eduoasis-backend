<!DOCTYPE html>
<html lang="en" style="font-family: Arial, sans-serif; background-color: #f4f4f7; padding: 0; margin: 0;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBT Exam Started</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f7;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f7; padding:20px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0"
                   style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);">

                <!-- Header -->
                <tr>
                    <td align="center" style="background-color:#16a34a; padding:30px 0;">
                        <h1 style="color:#ffffff; font-size:24px; margin:0;">📝 CBT Exam Started</h1>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:30px;">
                        <h2 style="color:#333333; font-size:22px; margin-top:0;">
                            Hello {{ $user->name }},
                        </h2>

                        <p style="color:#555555; font-size:16px; line-height:1.6;">
                            Your <strong>CBT exam</strong> has started successfully. You can continue immediately.
                        </p>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0;">
                            <tr>
                                <td style="padding:15px; background-color:#f0fdf4; border-left:4px solid #16a34a; border-radius:4px;">
                                    <p style="margin:0; font-size:16px;"><strong>Exam ID:</strong> {{ $examId }}</p>
                                    <p style="margin:5px 0 0; font-size:16px;"><strong>Status:</strong> Ongoing</p>
                                </td>
                            </tr>
                        </table>

                        <p style="color:#555555; font-size:16px; line-height:1.6;">
                            Click the button below to continue your exam. Complete it before the time expires.
                        </p>

                        <p style="text-align:center; margin:30px 0;">
                            <a href="{{ config('app.frontend_url') . '/cbt/exams/' . $examId }}"
                               style="background-color:#16a34a; color:#ffffff; text-decoration:none; padding:14px 28px; border-radius:6px; font-size:16px; display:inline-block;">
                                ▶ Continue Exam
                            </a>
                        </p>

                        <p style="color:#777777; font-size:14px; line-height:1.6;">
                            ⏱️ Note: Once the exam time elapses, your answers will be automatically submitted.
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="background-color:#f4f4f7; padding:20px;">
                        <p style="color:#999999; font-size:12px; margin:0;">
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
