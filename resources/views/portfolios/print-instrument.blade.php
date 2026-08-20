<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Print — {{ $instrument->description }}</title>
        <style>
            * { box-sizing: border-box; }
            body { font-family: 'Segoe UI', Arial, sans-serif; color: #0f172a; margin: 24px; font-size: 13px; }
            h1 { font-size: 20px; margin: 0 0 4px; }
            h2 { font-size: 14px; margin: 24px 0 8px; text-transform: uppercase; letter-spacing: .05em; color: #475569; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px; }
            .company { color: #64748b; font-size: 12px; }
            .meta { display: flex; justify-content: space-between; margin-top: 8px; }
            table { width: 100%; border-collapse: collapse; margin-top: 8px; }
            th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; font-size: 12px; }
            th { background: #f1f5f9; }
            .summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 12px; }
            .summary div { border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; }
            .summary .label { color: #64748b; font-size: 11px; text-transform: uppercase; }
            .summary .value { font-size: 16px; font-weight: 700; margin-top: 2px; }
            .no-print { margin-bottom: 16px; }
            @media print {
                .no-print { display: none; }
            }
        </style>
    </head>
    <body>
        <div class="no-print">
            <button onclick="window.print()" style="padding:8px 16px;background:#EB721E;color:#fff;border:0;border-radius:6px;cursor:pointer;font-weight:600;">Print</button>
            <a href="{{ route('portfolios.instruments.pdf', [$portfolio, $instrument]) }}" style="margin-left:8px;padding:8px 16px;background:#fff;color:#EB721E;border:1px solid #EB721E;border-radius:6px;text-decoration:none;font-weight:600;">Export PDF</a>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:flex-start;">
            <div>
                <h1>{{ $company }}</h1>
                <p class="company">Investment Client Portfolio Management System</p>
            </div>
            <div style="text-align:right;">
                <p style="font-weight:700;margin:0;">Fixed Debt Instrument Report</p>
                <p class="company">Generated {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="meta">
            <div>
                <p><strong>Client:</strong> {{ $instrument->portfolio->client->full_name }} ({{ $instrument->portfolio->client->client_id }})</p>
                <p><strong>Description:</strong> {{ $instrument->description }}</p>
            </div>
            <div style="text-align:right;">
                <p><strong>Amount:</strong> {{ format_money($instrument->amount) }}</p>
                <p><strong>Status:</strong> {{ $instrument->status }}</p>
            </div>
        </div>

        <h2>Instrument Details</h2>
        @if ($instrument->detail)
            <table>
                <tr>
                    <th>Subscription Date</th><td>{{ format_date($instrument->detail->subscription_date) }}</td>
                    <th>Instrument</th><td>{{ $instrument->detail->instrument }}</td>
                    <th>Tenor</th><td>{{ $instrument->detail->tenor ?? '—' }}</td>
                    <th>Rental Rate</th><td>{{ $instrument->detail->rental_rate !== null ? $instrument->detail->rental_rate.'%' : '—' }}</td>
                    <th>Settlement Date</th><td>{{ format_date($instrument->detail->settlement_date) }}</td>
                </tr>
            </table>
        @else
            <p>No instrument details recorded.</p>
        @endif

        <h2>Payment Breakdown</h2>
        <table>
            <thead>
                <tr><th>ROI</th><th>Amount</th><th>Payment Date</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse ($instrument->paymentBreakdowns as $breakdown)
                    <tr>
                        <td>{{ $breakdown->roi }}</td>
                        <td>{{ format_money($breakdown->amount) }}</td>
                        <td>{{ format_date($breakdown->payment_date) }}</td>
                        <td>{{ $breakdown->label }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No payment breakdowns.</td></tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>