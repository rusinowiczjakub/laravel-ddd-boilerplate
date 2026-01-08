<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 40px 20px;
            line-height: 1.6;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
        }

        .email-card {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 48px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .logo {
            width: 48px;
            height: 48px;
            background-color: #ffffff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 0 32px 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #18181b;
            margin: 0 0 16px 0;
            line-height: 1.2;
        }

        p {
            font-size: 15px;
            color: #52525b;
            margin: 0 0 16px 0;
        }

        .code-box {
            background-color: #e4e4e7;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            margin: 24px 0;
        }

        .code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 4px;
            color: #18181b;
        }

        .signature {
            margin-top: 32px;
            color: #52525b;
        }

        .signature strong {
            color: #18181b;
        }

        .footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 32px;
            color: #71717a;
            font-size: 13px;
        }

        @media only screen and (max-width: 600px) {
            .email-card {
                padding: 32px 24px;
            }

            h1 {
                font-size: 24px;
            }

            .code {
                font-size: 24px;
                letter-spacing: 2px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-card">
            @yield('content')
        </div>

        <div class="footer">
            {{ $footer ?? '© ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.' }}
        </div>
    </div>
</body>
</html>
