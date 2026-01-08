<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Zen Arts')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8fafc;
            padding: 20px;
            line-height: 1.6;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(to right, #4F46E5, #4338CA);
            padding: 32px 40px;
            text-align: center;
        }

        .logo-container {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background-color: #ffffff;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            color: #4F46E5;
        }

        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #ffffff;
        }

        .header-title {
            color: #ffffff;
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }

        .content {
            padding: 40px;
            color: #334155;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #0f172a;
        }

        .message {
            margin-bottom: 24px;
            line-height: 1.7;
        }

        .button {
            display: inline-block;
            padding: 14px 28px;
            background-color: #4F46E5;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 16px 0;
            transition: background-color 0.2s;
        }

        .button:hover {
            background-color: #4338CA;
        }

        .info-box {
            background-color: #f1f5f9;
            border-left: 4px solid #4F46E5;
            padding: 16px 20px;
            margin: 24px 0;
            border-radius: 4px;
        }

        .info-box-success {
            background-color: #f0fdf4;
            border-left-color: #22c55e;
        }

        .info-box-warning {
            background-color: #fef3c7;
            border-left-color: #f59e0b;
        }

        .info-box-error {
            background-color: #fef2f2;
            border-left-color: #ef4444;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin: 24px 0;
        }

        .stat-card {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .footer {
            background-color: #0f172a;
            color: #94a3b8;
            padding: 32px 40px;
            text-align: center;
            font-size: 14px;
        }

        .footer-links {
            margin-bottom: 16px;
        }

        .footer-links a {
            color: #cbd5e1;
            text-decoration: none;
            margin: 0 12px;
        }

        .footer-links a:hover {
            color: #ffffff;
        }

        .social-links {
            margin: 16px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #cbd5e1;
            text-decoration: none;
        }

        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 32px 0;
        }

        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }

            .content {
                padding: 24px;
            }

            .header {
                padding: 24px;
            }

            .footer {
                padding: 24px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo-container">
                <div class="logo-icon">P</div>
                <div class="logo-text">Zen Arts</div>
            </div>
            @hasSection('header-title')
                <h1 class="header-title">@yield('header-title')</h1>
            @endif
        </div>

        <!-- Content -->
        <div class="content">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-links">
                <a href="{{ config('app.url') }}">Strona główna</a>
                <a href="{{ config('app.url') }}/help">Pomoc</a>
                <a href="{{ config('app.url') }}/contact">Kontakt</a>
            </div>

            <p style="margin: 16px 0; font-size: 13px;">
                Platforma dla designerów, którzy chcą zarabiać na swoich projektach.
            </p>

            <p style="font-size: 12px; color: #64748b;">
                © {{ date('Y') }} Zen Arts. Wszystkie prawa zastrzeżone.
            </p>

            <p style="font-size: 11px; color: #475569; margin-top: 16px;">
                Jeśli masz pytania, odpowiedz na tego maila lub skontaktuj się z nami na <a href="mailto:support@postershub.com" style="color: #4F46E5;">support@postershub.com</a>
            </p>
        </div>
    </div>
</body>
</html>
