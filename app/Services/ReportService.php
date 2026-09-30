<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Portfolio;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    public function portfolioPdf(Portfolio $portfolio): \Barryvdh\DomPDF\PDF
    {
        $portfolio->load([
            'client',
            'fixedDebtInstruments.detail',
            'fixedDebtInstruments.paymentBreakdowns',
            'paymentHistories',
            'equities',
        ]);

        $pdf = Pdf::loadView('reports.pdf.portfolio', [
            'portfolio' => $portfolio,
            'company' => setting('company_name', 'Yabar Finance Consult Limited'),
            'company_logo' => report_logo_path(),
        ]);

        return $pdf->setPaper('a4', 'portrait');
    }

    public function clientPdf(array $clientIds, array $fields): \Barryvdh\DomPDF\PDF
    {
        $clients = Client::with(['stateOfOrigin', 'lga', 'bank', 'nextOfKin'])
            ->whereIn('id', $clientIds)
            ->orderBy('surname')
            ->get();

        $pdf = Pdf::loadView('reports.pdf.client', [
            'clients' => $clients,
            'fields' => $fields,
            'company' => setting('company_name', 'Yabar Finance Consult Limited'),
            'company_logo' => report_logo_path(),
        ]);

        return $pdf->setPaper('a4', 'landscape');
    }
}
