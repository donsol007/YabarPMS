<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ClientPortfolioAccessController extends Controller
{
    public function show(string $token)
    {
        $client = $this->clientFromToken($token);

        if ($this->isUnlocked($client)) {
            return redirect()->route('portfolio.access.view', $token);
        }

        return view('client-portfolio-access.code', [
            'token' => $token,
            'client' => $client,
        ]);
    }

    public function unlock(Request $request, string $token)
    {
        $client = $this->clientFromToken($token);

        $request->validate([
            'access_code' => ['required', 'string', 'max:20'],
        ]);

        if (! $client->verifyPortfolioAccessCode((string) $request->input('access_code'))) {
            return back()
                ->withInput($request->only('access_code'))
                ->withErrors(['access_code' => 'The access code you entered is incorrect.']);
        }

        $request->session()->put($this->sessionKey($client), now()->timestamp);

        return redirect()->route('portfolio.access.view', $token);
    }

    public function view(Request $request, string $token)
    {
        $client = $this->clientFromToken($token);

        if (! $this->isUnlocked($client)) {
            return redirect()->route('portfolio.access.show', $token);
        }

        $portfolio = $client->portfolio?->load([
            'fixedDebtInstruments.detail',
            'fixedDebtInstruments.paymentBreakdowns',
            'paymentHistories',
            'equities',
        ]);

        return view('client-portfolio-access.show', [
            'token' => $token,
            'client' => $client,
            'portfolio' => $portfolio,
        ]);
    }

    public function pdf(Request $request, string $token, ReportService $reports)
    {
        $client = $this->clientFromToken($token);

        if (! $this->isUnlocked($client)) {
            return redirect()->route('portfolio.access.show', $token);
        }

        abort_unless($client->portfolio, 404);

        $pdf = $reports->portfolioPdf($client->portfolio);

        return $pdf->download('portfolio-report-'.$client->client_id.'.pdf');
    }

    public function lock(Request $request, string $token)
    {
        $client = $this->clientFromToken($token);

        $request->session()->forget($this->sessionKey($client));

        return redirect()->route('portfolio.access.show', $token);
    }

    private function clientFromToken(string $token): Client
    {
        $client = Client::query()
            ->where('portfolio_access_token', $token)
            ->whereNotNull('portfolio_access_code')
            ->first();

        abort_if($client === null || ! $client->hasPortfolioAccess(), 404);

        return $client;
    }

    private function isUnlocked(Client $client): bool
    {
        return session()->has($this->sessionKey($client));
    }

    private function sessionKey(Client $client): string
    {
        return 'portfolio_access_verified_'.$client->id;
    }
}
