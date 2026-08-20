<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable([
    'fixed_debt_instrument_id', 'subscription_date', 'instrument',
    'tenor', 'rental_rate', 'settlement_date',
])]
class InstrumentDetail extends Model
{
    use HasFactory, LogsActivity;

    protected function casts(): array
    {
        return [
            'subscription_date' => 'date',
            'settlement_date' => 'date',
            'rental_rate' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('instrument_detail');
    }

    public function fixedDebtInstrument(): BelongsTo
    {
        return $this->belongsTo(FixedDebtInstrument::class);
    }
}