<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Portfolio Report — {{ $portfolio->client->full_name }}</title>
        <style>
            * { box-sizing: border-box; }
            body { font-family: 'DejaVu Sans', sans-serif; color: #0f172a; margin: 24px; font-size: 12px; }
            h1 { font-size: 20px; margin: 0 0 4px; }
            h2 { font-size: 14px; margin: 20px 0 8px; text-transform: uppercase; letter-spacing: .05em; color: #334155; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px; }
            h3 { font-size: 12px; margin: 14px 0 6px; color: #334155; }
            .company { color: #64748b; font-size: 12px; }
            .header { display: flex; justify-content: space-between; align-items: flex-start; }
            .meta { margin-top: 8px; }
            table { width: 100%; border-collapse: collapse; margin-top: 6px; }
            th, td { border: 1px solid #cbd5e1; padding: 5px 7px; text-align: left; font-size: 11px; }
            th { background: #f1f5f9; }
            .summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 12px; }
            .summary div { border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; }
            .summary .label { color: #64748b; font-size: 10px; text-transform: uppercase; }
            .summary .value { font-size: 15px; font-weight: 700; margin-top: 2px; }
            .footer { margin-top: 32px; font-size: 10px; color: #94a3b8; text-align: center; }
        </style>
    </head>
    <body>
        <div class="header" style="text-align:center;">
            @if ($company_logo)
                <img src="{{ $company_logo }}" alt="logo" style="height:64px; margin-bottom:8px;" />
            @endif
            <div>
                <h1 style="text-align:center;text-transform: uppercase;">{{ $company }}</h1>
                <h2 style="text-align:center;text-transform: uppercase;">PORTFOLIO VALUATION FOR <br/>{{ $portfolio->client->full_name }}</h2>
            </div>

        </div>

        <h2 style="text-transform: uppercase;">Fixed Debt Instruments</h2>
        @forelse ($portfolio->fixedDebtInstruments as $instrument)
            <h2>CP PAYMENT DETAILS [{{ $instrument->description }}] ({{ format_money($instrument->amount) }})</h2>
            <table>
                <tr><th>Amount</th><td>{{ format_money($instrument->amount) }}</td><th>Status</th><td>{{ $instrument->status }}</td></tr>
            </table>
            @if ($instrument->detail)
                <table>
                    <tr>
                        <th>Subscription Date</th><td>{{ format_date($instrument->detail->subscription_date) }}</td>
                        <th>Instrument</th><td>{{ $instrument->detail->instrument }}</td>
                        <th>Tenor</th><td>{{ $instrument->detail->tenor ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Rental Rate</th><td>{{ $instrument->detail->rental_rate !== null ? $instrument->detail->rental_rate.'%' : '—' }}</td>
                        <th>Settlement Date</th><td>{{ format_date($instrument->detail->settlement_date) }}</td>
                        <th></th><td></td>
                    </tr>
                </table>
            @endif
            <h3>Payment Breakdown</h3>
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
        @empty
            <p>No fixed debt instruments.</p>
        @endforelse

        <h2>Payment History</h2>
        <table>
            <thead><tr><th>Date</th><th>Amount Paid</th><th>Payment Status</th></tr></thead>
            <tbody>
                @forelse ($portfolio->paymentHistories as $history)
                    <tr>
                        <td>{{ format_date($history->date) }}</td>
                        <td>{{ format_money($history->amount_paid) }}</td>
                        <td>{{ $history->label }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">No payment history.</td></tr>
                @endforelse
            </tbody>
        </table>

        <h2>Equity</h2>
        <table>
            <thead><tr><th>Stock</th><th>Unit</th><th>Price</th><th>Value</th></tr></thead>
            <tbody>
                @forelse ($portfolio->equities as $equity)
                    <tr>
                        <td>{{ $equity->stock }}</td>
                        <td>{{ number_format((float) $equity->unit, 4) }}</td>
                        <td>{{ format_money($equity->price) }}</td>
                        <td>{{ format_money($equity->value) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No equity holdings.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if (filled($portfolio->additional_information))
            <h2>Additional Information</h2>
            <p>{!! nl2br(e($portfolio->additional_information)) !!}</p>
        @endif

         <div class="summary">
            <div><div class="label">Total Fixed Debt</div><div class="value">{{ format_money($portfolio->total_fixed_debt) }}</div></div>
            <div><div class="label">Total Equity Value</div><div class="value">{{ format_money($portfolio->total_equity_value) }}</div></div>
            <div><div class="label">Total Portfolio Value</div><div class="value">{{ format_money($portfolio->total_portfolio_value) }}</div></div>
        </div>

        <div class="footer">This report was generated by the {{ $company }} system on {{ now()->format('d/m/Y H:i') }}.</div>
    </body>
</html>
