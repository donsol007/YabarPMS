<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Portfolio Report</title>
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
                <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">Dear {{ $portfolio->client->full_name }},</p>
                <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">Please find attached your portfolio valuation report. It contains a summary of your fixed debt instruments, payment history and equity holdings.</p>

                <table style="width:100%; border-collapse:collapse; margin:24px 0; font-size:14px;">
                    <tr>
                        <td style="padding:8px 12px; border-bottom:1px solid #e2e8f0; color:#64748b;">Total Fixed Debt</td>
                        <td style="padding:8px 12px; border-bottom:1px solid #e2e8f0; text-align:right; font-weight:700;">{{ format_money($portfolio->total_fixed_debt) }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 12px; border-bottom:1px solid #e2e8f0; color:#64748b;">Total Equity Value</td>
                        <td style="padding:8px 12px; border-bottom:1px solid #e2e8f0; text-align:right; font-weight:700;">{{ format_money($portfolio->total_equity_value) }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 12px; color:#64748b;">Total Portfolio Value</td>
                        <td style="padding:8px 12px; text-align:right; font-weight:700;">{{ format_money($portfolio->total_portfolio_value) }}</td>
                    </tr>
                </table>

                <p style="margin:0 0 8px; font-size:14px; line-height:1.6; color:#334155;">If you have any questions about this report, please don't hesitate to contact us.</p>
            </div>

            <div style="background-color:#f8fafc; border-top:1px solid #e2e8f0; padding:16px 28px; text-align:center; font-size:12px; color:#94a3b8;">
                {{ setting('company_name', 'Yabar Finance Consult Limited') }} · Investment Client Portfolio Management System
            </div>
        </div>
    </div>
</body>
</html>