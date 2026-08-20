<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Portfolio;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalClients = Client::count();

        $portfolios = Portfolio::with(['client'])->get();

        $totalInvestment = round($portfolios->sum(fn (Portfolio $p) => $p->total_portfolio_value), 2);

        $totalActivePortfolios = Portfolio::whereHas('fixedDebtInstruments', fn ($q) => $q->where('status', 'Active'))->count();

        $recentRegistrations = Client::where('created_at', '>=', now()->subDays(7))->count();

        // Investment distribution (pie) — total CP (fixed debt) vs total equity across all clients
        $distribution = [
            [
                'label' => 'CP',
                'value' => round($portfolios->sum(fn (Portfolio $p) => $p->total_fixed_debt), 2),
            ],
            [
                'label' => 'Equity',
                'value' => round($portfolios->sum(fn (Portfolio $p) => $p->total_equity_value), 2),
            ],
        ];

        // Portfolio performance (line) — cumulative total portfolio value per month, last 12 months
        $performance = [];
        $end = Carbon::now()->startOfMonth();
        for ($i = 11; $i >= 0; $i--) {
            $month = $end->copy()->subMonths($i);
            $value = $portfolios
                ->filter(fn (Portfolio $p) => $p->created_at?->startOfMonth()->lte($month))
                ->sum(fn (Portfolio $p) => $p->total_portfolio_value);

            $performance[] = [
                'label' => $month->format('M Y'),
                'value' => round($value, 2),
            ];
        }

        // Monthly investments (bar) — client amount_to_invest grouped by month, last 12 months
        $monthly = [];
        $clientsByMonth = Client::query()
            ->where('created_at', '>=', $end->copy()->subMonths(11)->startOfMonth())
            ->get()
            ->groupBy(fn (Client $c) => $c->created_at->format('Y-m'));

        for ($i = 11; $i >= 0; $i--) {
            $month = $end->copy()->subMonths($i);
            $value = collect($clientsByMonth[$month->format('Y-m')] ?? [])
                ->sum(fn (Client $c) => (float) $c->amount_to_invest);

            $monthly[] = [
                'label' => $month->format('M Y'),
                'value' => round($value, 2),
            ];
        }

        return view('dashboard', [
            'stats' => [
                'total_clients' => $totalClients,
                'total_investment' => $totalInvestment,
                'total_active_portfolios' => $totalActivePortfolios,
                'recent_registrations' => $recentRegistrations,
            ],
            'distribution' => $distribution,
            'performance' => $performance,
            'monthly' => $monthly,
        ]);
    }
}