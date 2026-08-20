<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Instrument — {{ $instrument->description }}</title>
        <style>
            * { box-sizing: border-box; }
            body { font-family: 'DejaVu Sans', sans-serif; color: #0f172a; margin: 24px; font-size: 12px; }
            h1 { font-size: 20px; margin: 0 0 4px; }
            h2 { font-size: 14px; margin: 20px 0 8px; text-transform: uppercase; letter-spacing: .05em; color: #334155; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px; }
            .company { color: #64748b; font-size: 12px; }
            .header { display: flex; justify-content: space-between; align-items: flex-start; }
            .meta { margin-top: 8px; }
            table { width: 100%; border-collapse: collapse; margin-top: 6px; }
            th, td { border: 1px solid #cbd5e1; padding: 5px 7px; text-align: left; font-size: 11px; }
            th { background: #f1f5f9; }
        </style>
    </head>
    <body>
        @if ($company_logo)
            <div style="text-align:center; margin-bottom:12px;">
                <img src="{{ $company_logo }}" alt="logo" style="height:56px;" />
            </div>
        @endif
        <div class="header">
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
            <p><strong>Client:</strong> {{ $instrument->portfolio->client->full_name }} ({{ $instrument->portfolio->client->client_id }})</p>
            <p><strong>Description:</strong> {{ $instrument->description }} &nbsp;·&nbsp; <strong>Amount:</strong> {{ format_money($instrument->amount) }} &nbsp;·&nbsp; <strong>Status:</strong> {{ $instrument->status }}</p>
        </div>

        <h2>Instrument Details</h2>
        @if ($instrument->detail)
            <table>
                <tr><th>Subscription Date</th><td>{{ format_date($instrument->detail->subscription_date) }}</td><th>Instrument</th><td>{{ $instrument->detail->instrument }}</td></tr>
                <tr><th>Tenor</th><td>{{ $instrument->detail->tenor ?? '—' }}</td><th>Rental Rate</th><td>{{ $instrument->detail->rental_rate !== null ? $instrument->detail->rental_rate.'%' : '—' }}</td></tr>
                <tr><th>Settlement Date</th><td>{{ format_date($instrument->detail->settlement_date) }}</td><th></th><td></td></tr>
            </table>
        @else
            <p>No instrument details recorded.</p>
        @endif

        <h2>Payment Breakdown</h2>
        <table>
            <thead><tr><th>ROI</th><th>Amount</th><th>Payment Date</th><th>Status</th></tr></thead>
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