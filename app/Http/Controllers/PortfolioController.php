<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\FixedDebtInstrument;
use App\Models\Portfolio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PortfolioController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Portfolio::class);

        return view('portfolios.index');
    }

    public function create()
    {
        $this->authorize('create', Portfolio::class);

        $clients = Client::query()
            ->approved()
            ->doesntHave('portfolio')
            ->orderBy('surname')
            ->get();

        return view('portfolios.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Portfolio::class);

        $data = $request->validate([
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')->where('status', Client::STATUS_APPROVED),
                'unique:portfolios,client_id',
            ],
        ]);

        $portfolio = Portfolio::create($data);

        activity()
            ->performedOn($portfolio)
            ->causedBy($request->user())
            ->withProperties(['client' => $portfolio->client->full_name])
            ->log('created portfolio');

        return redirect()->route('portfolios.show', $portfolio)
            ->with('toast', ['type' => 'success', 'message' => 'Portfolio created successfully.']);
    }

    public function show(Portfolio $portfolio)
    {
        $this->authorize('view', $portfolio);

        $portfolio->load(['client']);

        return view('portfolios.show', compact('portfolio'));
    }

    public function edit(Portfolio $portfolio)
    {
        $this->authorize('update', $portfolio);

        $portfolio->load(['client']);

        return view('portfolios.edit', compact('portfolio'));
    }

    public function update(Request $request, Portfolio $portfolio)
    {
        $this->authorize('update', $portfolio);

        $data = $request->validate([
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')->where('status', Client::STATUS_APPROVED),
                'unique:portfolios,client_id,'.$portfolio->id,
            ],
        ]);

        $portfolio->update($data);

        activity()
            ->performedOn($portfolio)
            ->causedBy($request->user())
            ->log('updated portfolio');

        return redirect()->route('portfolios.show', $portfolio)
            ->with('toast', ['type' => 'success', 'message' => 'Portfolio updated successfully.']);
    }

    public function destroy(Portfolio $portfolio)
    {
        $this->authorize('delete', $portfolio);

        $portfolio->delete();

        activity()
            ->performedOn($portfolio)
            ->causedBy(request()->user())
            ->log('deleted portfolio');

        return redirect()->route('portfolios.index')
            ->with('toast', ['type' => 'success', 'message' => 'Portfolio deleted successfully.']);
    }

    public function printInstrument(Portfolio $portfolio, FixedDebtInstrument $instrument)
    {
        $this->authorize('view', $portfolio);
        abort_unless($instrument->portfolio_id === $portfolio->id, 404);

        $instrument->load(['detail', 'paymentBreakdowns', 'portfolio.client']);

        return view('portfolios.print-instrument', [
            'portfolio' => $portfolio,
            'instrument' => $instrument,
            'company' => setting('company_name', 'Yabar Finance Consult Limited'),
            'report_logo' => report_logo_path(),
        ]);
    }

    public function exportInstrumentPdf(Portfolio $portfolio, FixedDebtInstrument $instrument)
    {
        $this->authorize('view', $portfolio);
        abort_unless($instrument->portfolio_id === $portfolio->id, 404);

        $instrument->load(['detail', 'paymentBreakdowns', 'portfolio.client']);

        $pdf = Pdf::loadView('reports.pdf.instrument', [
            'portfolio' => $portfolio,
            'instrument' => $instrument,
            'company' => setting('company_name', 'Yabar Finance Consult Limited'),
            'company_logo' => report_logo_path(),
        ]);

        return $pdf->download('instrument-'.str($instrument->id).'.pdf');
    }
}
