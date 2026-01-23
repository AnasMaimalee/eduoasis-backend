<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EduOasis CBT Result Slip</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #1f2937;
            background-color: #ecfdf5;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 26px rgba(16,185,129,0.15);
        }

        .header {
            background: linear-gradient(135deg,#10b981,#047857);
            color: white;
            padding: 25px 15px;
            text-align: center;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }

        .section {
            padding: 20px 25px;
        }

        .info-table td {
            padding: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        th {
            background: #10b981;
            color: white;
            padding: 10px;
            font-size: 13px;
            font-weight: 600;
        }

        td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .red {
            color: #dc2626;
            font-weight: 600;
        }

        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #065f46;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="header">
        <h2>EduOasis CBT Result Slip</h2>
    </div>

    <div class="section">
        <table class="info-table">
            <tr>
                <td><strong>Name:</strong></td>
                <td>{{ $exam->user->name }}</td>
            </tr>
            <tr>
                <td><strong>Exam ID:</strong></td>
                <td>{{ $exam->id }}</td>
            </tr>
            <tr>
                <td><strong>Total Score:</strong></td>
                <td>{{ $exam->total_score }}</td>
            </tr>
            <tr>
                <td><strong>Time Used:</strong></td>
                <td>{{ gmdate('H:i:s', $exam->time_used_seconds) }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table>
            <thead>
            <tr>
                <th>Subject</th>
                <th>Total</th>
                <th>Correct</th>
                <th>Wrong</th>
                <th>Score</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($breakdown['subjects'] as $row)
                <tr>
                    <td>{{ $row['subject'] }}</td>
                    <td>{{ $row['total_questions'] }}</td>
                    <td>{{ $row['correct'] }}</td>
                    <td class="red">{{ $row['wrong'] }}</td>
                    <td>{{ $row['score'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} CBT Examination System</p>
        <p>Powered by TechBridge Technology</p>
    </div>

</div>

</body>
</html>
