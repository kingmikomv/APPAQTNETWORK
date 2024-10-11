<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
        }
        .header img {
            height: 80px;
        }
        .content {
            padding: 20px;
        }
        .content h1 {
            color: #6E42D9;
            font-size: 24px;
        }
        .content p {
            font-size: 16px;
            line-height: 1.5;
            color: #555555;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            margin: 20px 0;
            background-color: #6E42D9;
            color: #ffffff;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            border-radius: 5px;
        }
        .footer {
            font-size: 12px;
            color: #999;
            text-align: center;
            padding-top: 20px;
        }
        .subcopy {
            font-size: 14px;
            color: #777777;
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
        }
        .subcopy a {
            color: #6E42D9;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="email-container">
        <!-- Header with logo -->
        <div class="header">
            <img src="{{ config('app.url') }}/assets/logo.png" alt="{{ config('app.name') }}">
        </div>

        <!-- Main content -->
        <div class="content">
            <h1>{{ $greeting ?? 'Hello!' }}</h1>

            <!-- Introductory Lines -->
            @foreach ($introLines as $line)
            <p>{{ $line }}</p>
            @endforeach

            <!-- Action Button -->
            @isset($actionText)
            <a href="{{ $actionUrl }}" class="button">{{ $actionText }}</a>
            @endisset

            <!-- Outro Lines -->
            @foreach ($outroLines as $line)
            <p>{{ $line }}</p>
            @endforeach

            <!-- Salutation -->
            <p>{{ $salutation ?? 'Best regards,' }}<br>{{ config('app.name') }}</p>
        </div>

        <!-- Subcopy -->
        @isset($actionText)
        <div class="subcopy">
            <p>If you're having trouble clicking the "{{ $actionText }}" button, copy and paste the URL below into your web browser:</p>
            <p><a href="{{ $actionUrl }}">{{ $actionUrl }}</a></p>
        </div>
        @endisset
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>

</body>
</html>
