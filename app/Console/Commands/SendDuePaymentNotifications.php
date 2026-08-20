<?php

namespace App\Console\Commands;

use App\Models\PaymentBreakdown;
use App\Models\PaymentHistory;
use App\Models\User;
use App\Notifications\PaymentDueNotification;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;

class SendDuePaymentNotifications extends Command
{
    protected $signature = 'notifications:due-payments';

    protected $description = 'Notify staff about ROI/payments becoming due within the configured notice window';

    public function handle(): int
    {
        $noticeDays = (int) setting('due_notice_days', 7);
        $start = now()->startOfDay();
        $end = now()->addDays($noticeDays)->endOfDay();
        $users = User::all();
        $sent = 0;

        $dueBreakdowns = PaymentBreakdown::query()
            ->with(['fixedDebtInstrument.portfolio.client'])
            ->whereIn('status', ['unpaid', 'blank'])
            ->whereBetween('payment_date', [$start, $end])
            ->get();

        foreach ($dueBreakdowns as $breakdown) {
            $ref = 'breakdown-'.$breakdown->id;

            if ($this->alreadyNotified($ref)) {
                continue;
            }

            $instrument = $breakdown->fixedDebtInstrument;
            $portfolio = $instrument?->portfolio;
            $client = $portfolio?->client;

            if (! $client) {
                continue;
            }

            Notification::send($users, new PaymentDueNotification(
                clientName: $client->full_name,
                description: $instrument->description,
                amount: format_money($breakdown->amount),
                dueDate: format_date($breakdown->payment_date),
                url: route('portfolios.show', $portfolio),
                ref: $ref,
            ));

            $sent++;
        }

        $dueHistories = PaymentHistory::query()
            ->with(['portfolio.client'])
            ->where('payment_status', 'unpaid')
            ->whereBetween('date', [$start, $end])
            ->get();

        foreach ($dueHistories as $history) {
            $ref = 'history-'.$history->id;

            if ($this->alreadyNotified($ref)) {
                continue;
            }

            $portfolio = $history->portfolio;
            $client = $portfolio?->client;

            if (! $client) {
                continue;
            }

            Notification::send($users, new PaymentDueNotification(
                clientName: $client->full_name,
                description: 'Payment',
                amount: format_money($history->amount_paid),
                dueDate: format_date($history->date),
                url: route('portfolios.show', $portfolio),
                ref: $ref,
            ));

            $sent++;
        }

        $this->info("Created {$sent} due-payment notification groups.");

        return self::SUCCESS;
    }

    protected function alreadyNotified(string $ref): bool
    {
        return DatabaseNotification::query()
            ->where('type', PaymentDueNotification::class)
            ->where('data->ref', $ref)
            ->exists();
    }
}