<?php

namespace App\Services;

use App\Mail\ClientPortfolioReport;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function isConfigured(): bool
    {
        return (bool) setting('mail_host') && (bool) setting('mail_from_address');
    }

    public function configure(): void
    {
        $scheme = match (setting('mail_encryption')) {
            'ssl' => 'smtps',
            default => 'smtp',
        };

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => setting('mail_host'),
            'mail.mailers.smtp.port' => (int) setting('mail_port', 587),
            'mail.mailers.smtp.username' => setting('mail_username'),
            'mail.mailers.smtp.password' => setting('mail_password'),
            'mail.mailers.smtp.scheme' => $scheme,
            'mail.from.address' => setting('mail_from_address'),
            'mail.from.name' => setting('mail_from_name', setting('company_name', config('app.name', 'Yabar ERP'))),
        ]);
    }

    public function sendPortfolioReport(Portfolio $portfolio): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $this->configure();

        $pdf = app(ReportService::class)->portfolioPdf($portfolio);

        Mail::send(new ClientPortfolioReport($portfolio, $pdf->output()));

        return true;
    }
}