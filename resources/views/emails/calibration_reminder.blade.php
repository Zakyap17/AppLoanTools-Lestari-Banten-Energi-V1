<!DOCTYPE html>
<html>

<head>
    <title>Calibration Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #004d99;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #004d99;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            margin-top: 20px;
        }

        .footer {
            margin-top: 20px;
            font-size: 0.8em;
            color: #777;
            text-align: center;
        }

        /* Badge colors */
        .text-warning {
            color: #d97706;
            font-weight: bold;
        }

        .text-danger {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Calibration Reminder</h2>
        </div>

        <div class="content">
            <p>Dear Management,</p>

            <p>This is an automated reminder that the following tool(s) are due for calibration soon. Please review
                their status and schedule a calibration if necessary.</p>

            <table>
                <thead>
                    <tr>
                        <th>Tool Name</th>
                        <th>Tool ID</th>
                        <th>Calibration Date</th>
                        <th>Days Left</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tools as $tool)
                        @php
                            $today = \Carbon\Carbon::now()->startOfDay();
                            $nextCalDate = \Carbon\Carbon::parse($tool->next_calibration_date)->startOfDay();
                            $daysLeft = (int) $today->diffInDays($nextCalDate, false);

                            $daysTextClass = 'text-warning';
                            if ($daysLeft <= 0) {
                                $daysTextClass = 'text-danger';
                                $daysText = abs($daysLeft) . ' Days Overdue';
                            } else {
                                if ($daysLeft <= 14) {
                                    $daysTextClass = 'text-danger';
                                }
                                $daysText = $daysLeft . ' Days Left';
                            }
                        @endphp
                        <tr>
                            <td>{{ $tool->tool_name }}</td>
                            <td>{{ $tool->tool_id }}</td>
                            <td>{{ $nextCalDate->format('d M Y') }}</td>
                            <td class="{{ $daysTextClass }}">{{ $daysText }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="text-align: center;">
                <a href="{{ url('/transaction/calibration-issuing') }}" class="btn">View Calibration Issuing</a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} PT. Lestari Banten Energi. All rights reserved.</p>
            <p>Automated System Notification. Please do not reply directly to this email.</p>
        </div>
    </div>
</body>

</html>