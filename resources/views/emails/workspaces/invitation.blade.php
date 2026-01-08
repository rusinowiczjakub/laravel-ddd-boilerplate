<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workspace Invitation</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1a1a1a;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .content {
            margin-bottom: 30px;
        }
        .content p {
            margin: 0 0 15px 0;
            color: #555;
        }
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #f3f4f6;
            border-radius: 4px;
            font-weight: 500;
            color: #374151;
            text-transform: capitalize;
        }
        .button {
            display: inline-block;
            padding: 14px 28px;
            background: #2563eb;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
        }
        .button:hover {
            background: #1d4ed8;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .link {
            color: #6b7280;
            word-break: break-all;
            font-size: 12px;
            margin-top: 15px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 You've been invited!</h1>
        </div>

        <div class="content">
            <p>Hello!</p>
            <p>You've been invited to join a workspace as a <span class="role-badge">{{ $role }}</span>.</p>
            <p>Click the button below to accept the invitation and get started:</p>

            <center>
                <a href="{{ url('/invitations/accept/' . $token) }}" class="button">
                    Accept Invitation
                </a>
            </center>

            <p class="link">
                Or copy and paste this link into your browser:<br>
                {{ url('/invitations/accept/' . $token) }}
            </p>
        </div>

        <div class="footer">
            <p>This invitation will expire in 7 days.</p>
            <p>If you didn't expect this invitation, you can safely ignore this email.</p>
        </div>
    </div>
</body>
</html>
