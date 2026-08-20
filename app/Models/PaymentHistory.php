<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable(['portfolio_id', 'date', 'amount_paid', 'payment_status'])]
class PaymentHistory extends Model
{
    use HasFactory, LogsActivity;

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('payment_history');
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function getLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Paid',
            'unpaid' => 'Pending',
            default => 'Not due',
        };
    }
}