<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Portfolio Access Link</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <div style="max-width:600px; margin:0 auto; padding:32px 16px;">
        <div style="background-color:#ffffff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
            <div style="background-color:#0f172a; padding:24px 28px; text-align:center;">
                @if (setting('company_logo'))
                    <img src="{{ asset('storage/'.setting('company_logo')) }}" alt="{{ setting('company_name') }}" style="height:56px; margin-bottom:8px;" />
                @endif
                <div style="color:#ffffff; font-size:18px; font-weight:700;">{{ setting('company_name', 'Yabar Finance Consult Limited') }}</div>
            </div>

            <div style="padding:28px;">
                <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">Dear {{ $client->full_name }},</p>
                <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">You can now view your investment portfolio online. Click the button below and enter the access code provided to you.</p>

                <div style="text-align:center; margin:28px 0;">
                    <a href="{{ $client->portfolioAccessUrl() }}"
                       style="display:inline-block; background-color:#2563eb; color:#ffffff; text-decoration:none; padding:12px 28px; border-radius:8px; font-size:15px; font-weight:700;">
                        View My Portfolio
                    </a>
                </div>

                <table style="width:100%; border-collapse:collapse; margin:8px 0 24px; font-size:14px;">
                    <tr>
                        <td style="padding:8px 12px; border-bottom:1px solid #e2e8f0; color:#64748b;">Access Code</td>
                        <td style="padding:8px 12px; border-bottom:1px solid #e2e8f0; text-align:right; font-weight:700; letter-spacing:1px;">{{ $client->portfolio_access_code }}</td>
                    </tr>
                </table>

                <p style="margin:0 0 8px; font-size:13px; line-height:1.6; color:#64748b;">If the button does not work, copy and paste this link into your browser:</p>
                <p style="margin:0 0 16px; font-size:13px; line-height:1.6; word-break:break-all; color:#2563eb;">{{ $client->portfolioAccessUrl() }}</p>

                <p style="margin:0; font-size:14px; line-height:1.6; color:#334155;">For your security, please do not share this link or access code with anyone.</p>
            </div>

            <div style="background-color:#f8fafc; border-top:1px solid #e2e8f0; padding:16px 28px; text-align:center; font-size:12px; color:#94a3b8;">
                {{ setting('company_name', 'Yabar Finance Consult Limited') }} · Investment Client Portfolio Management System
            </div>
        </div>
    </div>
</body>
</html>