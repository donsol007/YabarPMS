<?php

namespace App\Mail;

use App\Models\Portfolio;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ClientPortfolioReport extends Mailable
{
    use Queueable;

    public function __construct(
        public Portfolio $portfolio,
        public string $pdfContent,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Portfolio Report — '.setting('company_name', config('app.name', 'Yabar ERP')),
            to: [$this->portfolio->client->email],
            from: new Address(
                (string) setting('mail_from_address', config('mail.from.address')),
                (string) setting('mail_from_name', setting('company_name', config('mail.from.name'))),
            ),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.client-portfolio-report');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => $this->pdfContent,
                'portfolio-report-'.$this->portfolio->client->client_id.'.pdf'
            )->withMime('application/pdf'),
        ];
    }
}