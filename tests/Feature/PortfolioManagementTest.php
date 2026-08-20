<?php

use App\Models\Client;
use App\Models\Equity;
use App\Models\FixedDebtInstrument;
use App\Models\PaymentBreakdown;
use App\Models\PaymentHistory;
use App\Models\Portfolio;
use Livewire\Livewire;

test('admin can create a portfolio for a client', function () {
    $user = adminUser();
    $client = Client::factory()->create();

    $this->actingAs($user)
        ->post('/portfolios', ['client_id' => $client->id])
        ->assertRedirect();

    expect(Portfolio::where('client_id', $client->id)->exists())->toBeTrue();
});

test('a client can only have one portfolio', function () {
    $user = adminUser();
    $client = Client::factory()->create();
    Portfolio::factory()->create(['client_id' => $client->id]);

    $this->actingAs($user)
        ->post('/portfolios', ['client_id' => $client->id])
        ->assertSessionHasErrors('client_id');
});

test('a pending client cannot be given a portfolio', function () {
    $user = adminUser();
    $client = Client::factory()->create(['status' => 'pending']);

    $this->actingAs($user)
        ->post('/portfolios', ['client_id' => $client->id])
        ->assertSessionHasErrors('client_id');

    expect(Portfolio::where('client_id', $client->id)->exists())->toBeFalse();
});

test('portfolio create form only lists approved clients', function () {
    $user = adminUser();
    $approved = Client::factory()->create(['status' => 'approved']);
    $pending = Client::factory()->create(['status' => 'pending']);

    $this->actingAs($user)
        ->get('/portfolios/create')
        ->assertOk()
        ->assertSee($approved->client_id)
        ->assertDontSee($pending->client_id);
});

test('admin can update and delete a portfolio', function () {
    $user = adminUser();
    $portfolio = Portfolio::factory()->create();
    $client = Client::factory()->create();

    $this->actingAs($user)
        ->put("/portfolios/{$portfolio->id}", ['client_id' => $client->id])
        ->assertRedirect();

    expect($portfolio->refresh()->client_id)->toBe($client->id);

    $this->actingAs($user)
        ->delete("/portfolios/{$portfolio->id}")
        ->assertRedirect('/portfolios');

    expect(Portfolio::find($portfolio->id))->toBeNull();
});

test('admin can add a fixed debt instrument with details', function () {
    $user = adminUser();
    $portfolio = Portfolio::factory()->create();

    Livewire::actingAs($user)
        ->test(\App\Livewire\PortfolioManager::class, ['portfolio' => $portfolio])
        ->call('openInstrumentCreate')
        ->set('instrumentForm.description', 'Commercial Paper 2026')
        ->set('instrumentForm.amount', '5000000')
        ->set('instrumentForm.status', 'Active')
        ->set('detailForm.subscription_date', '2026-01-01')
        ->set('detailForm.instrument', 'CP')
        ->set('detailForm.tenor', '90 days')
        ->set('detailForm.rental_rate', '12.5')
        ->set('detailForm.settlement_date', '2026-04-01')
        ->call('saveInstrument')
        ->assertHasNoErrors();

    $instrument = FixedDebtInstrument::first();

    expect($instrument)->not->toBeNull()
        ->and($instrument->portfolio_id)->toBe($portfolio->id)
        ->and($instrument->detail)->not->toBeNull()
        ->and($instrument->detail->tenor)->toBe('90 days');
});

test('roi field auto-prefills with sequential labels', function () {
    $user = adminUser();
    $portfolio = Portfolio::factory()->create();
    $instrument = FixedDebtInstrument::create([
        'portfolio_id' => $portfolio->id,
        'description' => 'CP',
        'amount' => 1000000,
        'status' => 'Active',
    ]);

    PaymentBreakdown::create([
        'fixed_debt_instrument_id' => $instrument->id,
        'roi' => 'ROI 1',
        'amount' => 100000,
        'payment_date' => '2026-09-01',
        'status' => 'unpaid',
    ]);
    PaymentBreakdown::create([
        'fixed_debt_instrument_id' => $instrument->id,
        'roi' => 'ROI 2',
        'amount' => 100000,
        'payment_date' => '2026-10-01',
        'status' => 'blank',
    ]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\PortfolioManager::class, ['portfolio' => $portfolio])
        ->call('openBreakdownCreate', $instrument->id)
        ->assertSet('breakdownForm.roi', 'ROI 3');
});

test('admin can add payment breakdown and history and equity', function () {
    $user = adminUser();
    $portfolio = Portfolio::factory()->create();
    $instrument = FixedDebtInstrument::create([
        'portfolio_id' => $portfolio->id,
        'description' => 'CP',
        'amount' => 1000000,
        'status' => 'Active',
    ]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\PortfolioManager::class, ['portfolio' => $portfolio])
        ->call('openBreakdownCreate', $instrument->id)
        ->set('breakdownForm.roi', 'ROI 1')
        ->set('breakdownForm.amount', '100000')
        ->set('breakdownForm.payment_date', '2026-09-01')
        ->set('breakdownForm.status', 'unpaid')
        ->call('saveBreakdown')
        ->assertHasNoErrors()
        ->call('openHistoryCreate')
        ->set('historyForm.date', '2026-08-01')
        ->set('historyForm.amount_paid', '100000')
        ->set('historyForm.payment_status', 'paid')
        ->call('saveHistory')
        ->assertHasNoErrors()
        ->call('openEquityCreate')
        ->set('equityForm.stock', 'GUARANTY')
        ->set('equityForm.unit', '1000')
        ->set('equityForm.price', '45.5')
        ->call('saveEquity')
        ->assertHasNoErrors();

    expect(PaymentBreakdown::count())->toBe(1)
        ->and(PaymentHistory::count())->toBe(1)
        ->and(Equity::count())->toBe(1)
        ->and((float) Equity::first()->value)->toBe(45500.0);
});

test('staff cannot mutate portfolios in the manager', function () {
    $user = staffUser();
    $portfolio = Portfolio::factory()->create();

    Livewire::actingAs($user)
        ->test(\App\Livewire\PortfolioManager::class, ['portfolio' => $portfolio])
        ->call('openInstrumentCreate')
        ->assertForbidden();
});

test('portfolio totals sum fixed debt and equity value', function () {
    $portfolio = Portfolio::factory()->create();

    FixedDebtInstrument::create(['portfolio_id' => $portfolio->id, 'description' => 'A', 'amount' => 3000000, 'status' => 'Active']);
    FixedDebtInstrument::create(['portfolio_id' => $portfolio->id, 'description' => 'B', 'amount' => 2000000, 'status' => 'Matured']);
    $portfolio->equities()->create(['stock' => 'GTB', 'unit' => 100, 'price' => 50]);

    expect($portfolio->total_fixed_debt)->toBe(5000000.0)
        ->and($portfolio->total_equity_value)->toBe(5000.0)
        ->and($portfolio->total_portfolio_value)->toBe(5005000.0);
});

test('portfolio instrument PDF route returns a file', function () {
    $user = adminUser();
    $portfolio = Portfolio::factory()->withInstruments()->create();
    $instrument = $portfolio->fixedDebtInstruments()->first();

    $this->actingAs($user)
        ->get("/portfolios/{$portfolio->id}/pdf/{$instrument->id}")
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');
});