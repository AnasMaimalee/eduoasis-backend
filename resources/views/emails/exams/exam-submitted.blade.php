<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CBT Exam Submitted</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9fafb; padding: 24px;">

    <div style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">

        <!-- HEADER -->
        <div style="background-color: #10b981; padding: 20px 24px;">
            <h1 style="margin: 0; color: #ffffff; font-size: 20px;">
                CBT Exam Submitted
            </h1>
        </div>

        <!-- BODY -->
        <div style="padding: 24px;">
            <p style="font-size: 16px; color: #111827;">
                Hello <strong>{{ $user->name }}</strong>,
            </p>

            <p style="color: #374151; line-height: 1.6;">
                Your CBT exam has been submitted successfully. You can now view your result using the button below.
            </p>

            <div style="margin: 28px 0;">
                <a
                    href="{{ $frontendUrl }}/user/cbt/results/{{ $examId }}"
                    style="
                        background-color: #10b981;
                        color: #ffffff;
                        padding: 12px 22px;
                        text-decoration: none;
                        border-radius: 6px;
                        font-weight: 600;
                        display: inline-block;
                    "
                >
                    View Result
                </a>
            </div>

            <p style="color: #6b7280; font-size: 14px;">
                Thank you for using our CBT platform.
            </p>
        </div>

    </div>

</body>
</html>
