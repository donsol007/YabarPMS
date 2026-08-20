<?php

namespace App\Livewire;

use App\Models\FixedDebtInstrument;
use App\Models\PaymentBreakdown;
use App\Models\PaymentHistory;
use App\Models\Portfolio;
use App\Models\Equity;
use Livewire\Component;

class PortfolioManager extends Component
{
    public Portfolio $portfolio;

    public string $additionalInformation = '';

    // Instrument + detail modal
    public bool $showInstrumentModal = false;

    public string $instrumentModalMode = 'create'; // create | edit | view

    public ?int $editingInstrumentId = null;

    public array $instrumentForm = [
        'description' => '',
        'amount' => '',
        'status' => 'Active',
    ];

    public array $detailForm = [
        'subscription_date' => '',
        'instrument' => 'CP',
        'tenor' => '',
        'rental_rate' => '',
        'settlement_date' => '',
    ];

    // Breakdown form (inside instrument modal)
    public bool $showBreakdownModal = false;

    public ?int $editingBreakdownId = null;

    public array $breakdownForm = [
        'roi' => '',
        'amount' => '',
        'payment_date' => '',
        'status' => 'blank',
    ];

    // Payment history modal
    public bool $showHistoryModal = false;

    public ?int $editingHistoryId = null;

    public array $historyForm = [
        'date' => '',
        'amount_paid' => '',
        'payment_status' => 'paid',
    ];

    // Equity modal
    public bool $showEquityModal = false;

    public ?int $editingEquityId = null;

    public array $equityForm = [
        'stock' => '',
        'unit' => '',
        'price' => '',
    ];

    public function mount(Portfolio $portfolio): void
    {
        $this->portfolio = $portfolio->load(['client']);
        $this->additionalInformation = (string) $portfolio->additional_information;
    }

    public function saveAdditionalInformation(): void
    {
        $this->authorize('update', $this->portfolio);

        $this->validate([
            'additionalInformation' => ['nullable', 'string', 'max:5000'],
        ]);

        $this->portfolio->update([
            'additional_information' => $this->additionalInformation === '' ? null : $this->additionalInformation,
        ]);

        $this->dispatch('toast', type: 'success', message: 'Additional information saved.');
    }

    public function canMutate(): bool
    {
        return auth()->user()->can('edit portfolios');
    }

    /* ------------------------------------------------------------------ */
    /*  Instruments + details                                              */
    /* ------------------------------------------------------------------ */

    public function openInstrumentCreate(): void
    {
        $this->authorize('update', $this->portfolio);

        $this->resetInstrumentForm();
        $this->instrumentModalMode = 'create';
        $this->editingInstrumentId = null;
        $this->showInstrumentModal = true;
    }

    public function openInstrumentEdit(int $id): void
    {
        $this->authorize('update', $this->portfolio);

        $instrument = FixedDebtInstrument::with(['detail'])->findOrFail($id);

        $this->instrumentForm = [
            'description' => $instrument->description,
            'amount' => (string) $instrument->amount,
            'status' => $instrument->status,
        ];

        $this->detailForm = $instrument->detail ? [
            'subscription_date' => $instrument->detail->subscription_date?->format('Y-m-d') ?? '',
            'instrument' => $instrument->detail->instrument ?? 'CP',
            'tenor' => $instrument->detail->tenor ?? '',
            'rental_rate' => $instrument->detail->rental_rate !== null ? (string) $instrument->detail->rental_rate : '',
            'settlement_date' => $instrument->detail->settlement_date?->format('Y-m-d') ?? '',
        ] : $this->defaultDetailForm();

        $this->editingInstrumentId = $id;
        $this->instrumentModalMode = 'edit';
        $this->showInstrumentModal = true;
    }

    public function openInstrumentView(int $id): void
    {
        $this->editingInstrumentId = $id;
        $this->instrumentModalMode = 'view';
        $this->showInstrumentModal = true;
    }

    public function saveInstrument(): void
    {
        $this->authorize('update', $this->portfolio);

        $this->validate([
            'instrumentForm.description' => ['required', 'string', 'max:191'],
            'instrumentForm.amount' => ['required', 'numeric', 'min:0'],
            'instrumentForm.status' => ['required', 'in:Active,Matured,Closed'],
            'detailForm.subscription_date' => ['nullable', 'date'],
            'detailForm.instrument' => ['nullable', 'string', 'max:50'],
            'detailForm.tenor' => ['nullable', 'string', 'max:50'],
            'detailForm.rental_rate' => ['nullable', 'numeric', 'min:0'],
            'detailForm.settlement_date' => ['nullable', 'date', 'after_or_equal:detailForm.subscription_date'],
        ]);

        if ($this->editingInstrumentId) {
            $instrument = FixedDebtInstrument::findOrFail($this->editingInstrumentId);
            $instrument->update($this->instrumentForm);
        } else {
            $instrument = $this->portfolio->fixedDebtInstruments()->create($this->instrumentForm);
        }

        $detailData = $this->detailForm;

        if (collect($detailData)->filter(fn ($v) => $v !== '' && $v !== null)->isEmpty()) {
            $instrument->detail()?->delete();
        } else {
            $instrument->detail()->updateOrCreate([], $detailData);
        }

        $this->showInstrumentModal = false;

        $this->dispatch('toast', type: 'success', message: 'Fixed debt instrument saved.');
    }

    public function deleteInstrument(int $id): void
    {
        $this->authorize('update', $this->portfolio);

        $instrument = FixedDebtInstrument::findOrFail($id);
        $instrument->delete();

        $this->dispatch('toast', type: 'success', message: 'Instrument deleted.');
    }

    /* ------------------------------------------------------------------ */
    /*  Payment breakdown (inside instrument modal)                        */
    /* ------------------------------------------------------------------ */

    public function openBreakdownCreate(int $instrumentId): void
    {
        $this->authorize('update', $this->portfolio);

        $next = PaymentBreakdown::where('fixed_debt_instrument_id', $instrumentId)->count() + 1;

        $this->editingInstrumentId = $instrumentId;
        $this->editingBreakdownId = null;
        $this->breakdownForm = ['roi' => "ROI {$next}", 'amount' => '', 'payment_date' => '', 'status' => 'blank'];
        $this->showBreakdownModal = true;
    }

    public function openBreakdownEdit(int $id): void
    {
        $this->authorize('update', $this->portfolio);

        $breakdown = PaymentBreakdown::findOrFail($id);
        $this->editingInstrumentId = $breakdown->fixed_debt_instrument_id;
        $this->editingBreakdownId = $id;
        $this->breakdownForm = [
            'roi' => (string) $breakdown->roi,
            'amount' => (string) $breakdown->amount,
            'payment_date' => $breakdown->payment_date?->format('Y-m-d') ?? '',
            'status' => $breakdown->status,
        ];
        $this->showBreakdownModal = true;
    }

    public function saveBreakdown(): void
    {
        $this->authorize('update', $this->portfolio);

        $this->validate([
            'breakdownForm.roi' => ['required', 'string', 'max:50'],
            'breakdownForm.amount' => ['required', 'numeric', 'min:0'],
            'breakdownForm.payment_date' => ['required', 'date'],
            'breakdownForm.status' => ['required', 'in:paid,unpaid,blank'],
        ]);

        $data = $this->breakdownForm;
        $data['fixed_debt_instrument_id'] = $this->editingInstrumentId;

        if ($this->editingBreakdownId) {
            PaymentBreakdown::findOrFail($this->editingBreakdownId)->update($data);
        } else {
            PaymentBreakdown::create($data);
        }

        $this->showBreakdownModal = false;

        $this->dispatch('toast', type: 'success', message: 'Payment breakdown saved.');
    }

    public function deleteBreakdown(int $id): void
    {
        $this->authorize('update', $this->portfolio);

        PaymentBreakdown::findOrFail($id)->delete();

        $this->dispatch('toast', type: 'success', message: 'Payment breakdown deleted.');
    }

    /* ------------------------------------------------------------------ */
    /*  Payment history                                                    */
    /* ------------------------------------------------------------------ */

    public function openHistoryCreate(): void
    {
        $this->authorize('update', $this->portfolio);

        $this->editingHistoryId = null;
        $this->historyForm = ['date' => '', 'amount_paid' => '', 'payment_status' => 'paid'];
        $this->showHistoryModal = true;
    }

    public function openHistoryEdit(int $id): void
    {
        $this->authorize('update', $this->portfolio);

        $history = PaymentHistory::findOrFail($id);
        $this->editingHistoryId = $id;
        $this->historyForm = [
            'date' => $history->date?->format('Y-m-d') ?? '',
            'amount_paid' => (string) $history->amount_paid,
            'payment_status' => $history->payment_status,
        ];
        $this->showHistoryModal = true;
    }

    public function saveHistory(): void
    {
        $this->authorize('update', $this->portfolio);

        $this->validate([
            'historyForm.date' => ['required', 'date'],
            'historyForm.amount_paid' => ['required', 'numeric', 'min:0'],
            'historyForm.payment_status' => ['required', 'in:paid,unpaid,blank'],
        ]);

        if ($this->editingHistoryId) {
            PaymentHistory::findOrFail($this->editingHistoryId)->update($this->historyForm);
        } else {
            $this->portfolio->paymentHistories()->create($this->historyForm);
        }

        $this->showHistoryModal = false;

        $this->dispatch('toast', type: 'success', message: 'Payment history saved.');
    }

    public function deleteHistory(int $id): void
    {
        $this->authorize('update', $this->portfolio);

        PaymentHistory::findOrFail($id)->delete();

        $this->dispatch('toast', type: 'success', message: 'Payment history deleted.');
    }

    /* ------------------------------------------------------------------ */
    /*  Equity                                                             */
    /* ------------------------------------------------------------------ */

    public function openEquityCreate(): void
    {
        $this->authorize('update', $this->portfolio);

        $this->editingEquityId = null;
        $this->equityForm = ['stock' => '', 'unit' => '', 'price' => ''];
        $this->showEquityModal = true;
    }

    public function openEquityEdit(int $id): void
    {
        $this->authorize('update', $this->portfolio);

        $equity = Equity::findOrFail($id);
        $this->editingEquityId = $id;
        $this->equityForm = [
            'stock' => $equity->stock,
            'unit' => (string) $equity->unit,
            'price' => (string) $equity->price,
        ];
        $this->showEquityModal = true;
    }

    public function saveEquity(): void
    {
        $this->authorize('update', $this->portfolio);

        $this->validate([
            'equityForm.stock' => ['required', 'string', 'max:191'],
            'equityForm.unit' => ['required', 'numeric', 'min:0'],
            'equityForm.price' => ['required', 'numeric', 'min:0'],
        ]);

        if ($this->editingEquityId) {
            Equity::findOrFail($this->editingEquityId)->update($this->equityForm);
        } else {
            $this->portfolio->equities()->create($this->equityForm);
        }

        $this->showEquityModal = false;

        $this->dispatch('toast', type: 'success', message: 'Equity saved.');
    }

    public function deleteEquity(int $id): void
    {
        $this->authorize('update', $this->portfolio);

        Equity::findOrFail($id)->delete();

        $this->dispatch('toast', type: 'success', message: 'Equity deleted.');
    }

    /* ------------------------------------------------------------------ */

    protected function resetInstrumentForm(): void
    {
        $this->instrumentForm = ['description' => '', 'amount' => '', 'status' => 'Active'];
        $this->detailForm = $this->defaultDetailForm();
    }

    protected function defaultDetailForm(): array
    {
        return [
            'subscription_date' => '',
            'instrument' => 'CP',
            'tenor' => '',
            'rental_rate' => '',
            'settlement_date' => '',
        ];
    }

    public function render()
    {
        $this->portfolio->load([
            'client',
            'fixedDebtInstruments.detail',
            'fixedDebtInstruments.paymentBreakdowns',
            'paymentHistories',
            'equities',
        ]);

        return view('livewire.portfolio-manager');
    }
}