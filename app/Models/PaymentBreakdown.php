<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable(['fixed_debt_instrument_id', 'roi', 'amount', 'payment_date', 'status'])]
class PaymentBreakdown extends Model
{
    use HasFactory, LogsActivity;

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('payment_breakdown');
    }

    public function fixedDebtInstrument(): BelongsTo
    {
        return $this->belongsTo(FixedDebtInstrument::class);
    }

    public function getLabelAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'Paid',
            'unpaid' => 'Pending',
            default => 'Not due',
        };
    }

    public function getBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'green',
            'unpaid' => 'amber',
            default => 'gray',
        };
    }
}