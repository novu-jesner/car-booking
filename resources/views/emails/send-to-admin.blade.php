<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vehicle Booking Request</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
            color: #2c3e50;
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('img/logo.png') }}" alt="novulutions logo">
        <h2>{{ $subject }}</h2>

        <div class="info"><strong>Requested By:</strong> {{ $booking->user->name }}</div>
        <div class="info"><strong>Title:</strong> {{ $booking->title }}</div>
        <div class="info"><strong>Purpose:</strong> {{ $booking->purpose }}</div>
        <div class="info"><strong>From Date:</strong> {{ $booking->from_date }}</div>
        <div class="info"><strong>To Date:</strong> {{ $booking->to_date }}</div>
        <div class="info"><strong>Destination:</strong> {{ $booking->destination }}</div>
        <div class="info"><strong>Driver Assigned:</strong> {{ $booking->driver->name ?? 'N/A' }}</div>
        <div class="info"><strong>Vehicle:</strong> {{ $booking->car->name ?? 'N/A' }}</div>
        <div class="info"><strong>Remarks:</strong> {{ $booking->remarks ?: 'None' }}</div>

        <div class="footer">
            This is an automated message from the Novulutions Vehicle Booking System.
        </div>
    </div>
</body>
</html>
