<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable(['portfolio_id', 'stock', 'unit', 'price', 'value'])]
class Equity extends Model
{
    use HasFactory, LogsActivity;

    protected function casts(): array
    {
        return [
            'unit' => 'decimal:4',
            'price' => 'decimal:2',
            'value' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('equity');
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    protected static function booted(): void
    {
        static::saving(function (Equity $equity) {
            $equity->value = round((float) $equity->unit * (float) $equity->price, 2);
        });
    }
}