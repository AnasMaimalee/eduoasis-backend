<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>EduOasis – Wallet Transactions</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
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
            text-align: center;
            background: linear-gradient(135deg,#10b981,#047857);
            padding: 20px;
            border-radius: 12px 12px 0 0;
            color: white;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
        }

        .meta {
            padding: 15px 25px;
            font-size: 14px;
            color: #064e3b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 25px 20px 25px;
        }

        th {
            background: #10b981;
            color: white;
            padding: 10px;
            font-size: 12px;
        }

        td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .credit { color: #16a34a; font-weight: 600; }
        .debit { color: #dc2626; font-weight: 600; }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #065f46;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="header">
        <h2>EduOasis – Wallet Transactions</h2>
        <small>{{ $generatedAt->format('Y-m-d H:i') }}</small>
    </div>

    <div class="meta">
        @if($user)
            <strong>User:</strong> {{ $user->name }} ({{ $user->email }})<br>
        @endif
    </div>

    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Before</th>
            <th>After</th>
            <th>Description</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach($transactions as $tx)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="{{ $tx->type }}">
                    {{ strtoupper($tx->type) }}
                </td>
                <td>₦{{ number_format($tx->amount, 2) }}</td>
                <td>₦{{ number_format($tx->balance_before, 2) }}</td>
                <td>₦{{ number_format($tx->balance_after, 2) }}</td>
                <td>{{ $tx->description }}</td>
                <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="footer">
        © {{ date('Y') }} EduOasis. All rights reserved.
    </div>

</div>

</body>
</html>
