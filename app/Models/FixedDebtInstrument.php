<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable(['portfolio_id', 'description', 'amount', 'status'])]
class FixedDebtInstrument extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('fixed_debt_instrument');
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function detail(): HasOne
    {
        return $this->hasOne(InstrumentDetail::class);
    }

    public function paymentBreakdowns(): HasMany
    {
        return $this->hasMany(PaymentBreakdown::class);
    }
}