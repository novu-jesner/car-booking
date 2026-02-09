<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Booking Request Rejected - Novulutions Vehicle Booking</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 20px;
            color: #2c3e50;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: relative;
        }
        .container img {
            position: absolute;
            top: 10px;
            right: 10px;
            height: 68px;
            width: auto;
        }
        h2 {
            color: #c0392b; /* red for rejection */
            margin-bottom: 20px;
        }
        .greeting {
            margin-bottom: 20px;
            font-size: 16px;
        }
        .info {
            margin-bottom: 12px;
        }
        .info strong {
            color: #34495e;
            display: inline-block;
            width: 140px;
        }
        .footer {
            margin-top: 30px;
            font-size: 13px;
            color: #7f8c8d;
            text-align: center;
        }
        .rejected-banner {
            background-color: #c0392b;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 10px 0;
            border-radius: 6px;
            margin-bottom: 25px;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('img/logo.png') }}" alt="Novulutions Logo" />

        <h2><i class="fas fa-times-circle text-danger"></i> Booking Request Rejected</h2>

        <p class="greeting">Hello {{ $booking->user->name }},</p>
        <p>We regret to inform you that your vehicle booking request has been <strong>rejected</strong>.</p>

        <div class="info"><strong>Title:</strong> {{ $booking->title }}</div>
        <div class="info"><strong>Purpose:</strong> {{ $booking->purpose }}</div>
        <div class="info"><strong>From Date:</strong> {{ $booking->from_date }}</div>
        <div class="info"><strong>To Date:</strong> {{ $booking->to_date }}</div>
        <div class="info"><strong>Destination:</strong> {{ $booking->destination }}</div>
        <div class="info"><strong>Remarks:</strong> {{ $booking->remarks ?: 'None' }}</div>

        <p>If you have any questions or believe this was made in error, please contact the admin team.</p>

        <div class="footer">
            This is an automated message from the Novulutions Vehicle Booking System.
        </div>
    </div>
</body>
</html>
