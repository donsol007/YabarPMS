<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Portfolio;
use App\Services\EmailService;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reports,
        private readonly EmailService $email,
    ) {
    }

    public function index()
    {
        $this->authorize('generate', Client::class);

        $portfolios = Portfolio::with('client')->get();
        $clients = Client::query()->get();

        return view('reports.index', compact('portfolios', 'clients'));
    }

    public function portfolio(Request $request)
    {
        $this->authorize('generate', Portfolio::class);

        $data = $request->validate([
            'portfolio_id' => ['required', 'exists:portfolios,id'],
        ]);

        $portfolio = Portfolio::findOrFail($data['portfolio_id']);

        activity()
            ->performedOn($portfolio)
            ->causedBy($request->user())
            ->log('generated portfolio report');

        $pdf = $this->reports->portfolioPdf($portfolio);

        return $pdf->download('portfolio-report-'.$portfolio->client->client_id.'.pdf');
    }

    public function sendPortfolio(Request $request)
    {
        $this->authorize('generate', Portfolio::class);

        $data = $request->validate([
            'portfolio_id' => ['required', 'exists:portfolios,id'],
        ]);

        $portfolio = Portfolio::findOrFail($data['portfolio_id']);

        if (! $portfolio->client || ! $portfolio->client->email) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'This client has no email address on file.',
            ]);
        }

        try {
            $sent = $this->email->sendPortfolioReport($portfolio);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Failed to send the email: '.$e->getMessage(),
            ]);
        }

        if (! $sent) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Email settings are not configured. Set them up under Email Settings first.',
            ]);
        }

        activity()
            ->performedOn($portfolio)
            ->causedBy($request->user())
            ->log('emailed portfolio report to '.$portfolio->client->email);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Report emailed to '.$portfolio->client->email.'.',
        ]);
    }

    public function client(Request $request)
    {
        $this->authorize('generate', Client::class);

        $data = $request->validate([
            'client_ids' => ['required', 'array', 'min:1'],
            'client_ids.*' => ['exists:clients,id'],
            'fields' => ['required', 'array', 'min:1'],
            'fields.*' => ['in:client_id,surname,first_name,middle_name,sex,date_of_birth,mobile_number,email,amount_to_invest,bank_name,account_name,account_number,bvn,occupation,employer_name,state,lga'],
        ]);

        $pdf = $this->reports->clientPdf($data['client_ids'], $data['fields']);

        activity()
            ->causedBy($request->user())
            ->log('generated client report');

        return $pdf->download('client-report-'.now()->format('Y-m-d').'.pdf');
    }
}