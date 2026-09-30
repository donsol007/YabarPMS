<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ClientPortfolioAccessLink extends Mailable
{
    use Queueable;

    public function __construct(
        public Client $client,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Portfolio Access Link — '.setting('company_name', config('app.name', 'Yabar ERP')),
            to: [$this->client->email],
            from: new Address(
                (string) setting('mail_from_address', config('mail.from.address')),
                (string) setting('mail_from_name', setting('company_name', config('mail.from.name'))),
            ),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.client-portfolio-access-link');
    }
}
