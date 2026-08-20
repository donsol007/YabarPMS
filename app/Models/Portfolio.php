<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable(['client_id', 'additional_information'])]
class Portfolio extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('portfolio');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function fixedDebtInstruments(): HasMany
    {
        return $this->hasMany(FixedDebtInstrument::class)->withTrashed();
    }

    public function paymentHistories(): HasMany
    {
        return $this->hasMany(PaymentHistory::class);
    }

    public function equities(): HasMany
    {
        return $this->hasMany(Equity::class);
    }

    public function getTotalFixedDebtAttribute(): float
    {
        return (float) $this->fixedDebtInstruments()->sum('amount');
    }

    public function getTotalEquityValueAttribute(): float
    {
        return (float) $this->equities()->sum('value');
    }

    public function getTotalPortfolioValueAttribute(): float
    {
        return $this->total_fixed_debt + $this->total_equity_value;
    }
}