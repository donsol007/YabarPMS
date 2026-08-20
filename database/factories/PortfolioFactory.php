<?php

namespace Database\Factories;

use App\Models\FixedDebtInstrument;
use App\Models\InstrumentDetail;
use App\Models\PaymentBreakdown;
use App\Models\PaymentHistory;
use App\Models\Portfolio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Portfolio>
 */
class PortfolioFactory extends Factory
{
    protected $model = Portfolio::class;

    public function definition(): array
    {
        return [
            'client_id' => \App\Models\Client::factory(),
        ];
    }

    public function withInstruments(): static
    {
        return $this->afterCreating(function (Portfolio $portfolio) {
            $instrument = FixedDebtInstrument::create([
                'portfolio_id' => $portfolio->id,
                'description' => 'Commercial Paper ' . now()->year,
                'amount' => 5000000,
                'status' => 'Active',
            ]);

            InstrumentDetail::create([
                'fixed_debt_instrument_id' => $instrument->id,
                'subscription_date' => now()->subMonths(3)->toDateString(),
                'instrument' => 'CP',
                'tenor' => '90 days',
                'rental_rate' => 12.5,
                'settlement_date' => now()->addMonths(3)->toDateString(),
            ]);

            for ($i = 0; $i < 4; $i++) {
                PaymentBreakdown::create([
                    'fixed_debt_instrument_id' => $instrument->id,
                    'roi' => 'ROI '.($i + 1),
                    'amount' => 250000,
                    'payment_date' => now()->addMonths($i)->toDateString(),
                    'status' => $i === 0 ? 'paid' : 'blank',
                ]);
            }

            PaymentHistory::create([
                'portfolio_id' => $portfolio->id,
                'date' => now()->subMonth()->toDateString(),
                'amount_paid' => 250000,
                'payment_status' => 'paid',
            ]);

            $portfolio->equities()->create([
                'stock' => 'GUARANTY',
                'unit' => 1000,
                'price' => 45.5,
                'value' => 45500,
            ]);
        });
    }
}