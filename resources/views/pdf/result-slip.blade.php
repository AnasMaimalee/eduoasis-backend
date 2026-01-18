<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #1f2937;
        }

        .header {
            background: #16a34a;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 6px;
        }

        .section {
            margin-top: 20px;
        }

        .info-table td {
            padding: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th {
            background: #16a34a;
            color: white;
            padding: 8px;
            font-size: 12px;
        }

        td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }
        .red{
            color: red;
        }
    </style>
</head>

<body>

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

</body>
</html>
