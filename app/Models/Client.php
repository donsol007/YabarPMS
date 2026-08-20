<?php

namespace App\Models;

use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable([
    'client_id', 'surname', 'first_name', 'middle_name', 'sex', 'date_of_birth',
    'mobile_number', 'mother_maiden_name', 'residential_address',
    'state_of_origin_id', 'lga_id', 'marital_status', 'religion', 'email',
    'amount_to_invest', 'bank_id', 'account_name', 'account_number',
    'account_type', 'bvn', 'account_opening_date', 'bank_address',
    'occupation', 'employer_name', 'employer_address', 'hobbies', 'created_by',
    'status',
])]
#[Hidden(['bvn'])]
class Client extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    /** @use HasFactory<ClientFactory> */
    use HasFactory, SoftDeletes, LogsActivity;

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'account_opening_date' => 'date',
            'amount_to_invest' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('client');
    }

    public function portfolio(): HasOne
    {
        return $this->hasOne(Portfolio::class)->withTrashed();
    }

    public function nextOfKin(): HasOne
    {
        return $this->hasOne(NextOfKin::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function stateOfOrigin(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_of_origin_id');
    }

    public function lga(): BelongsTo
    {
        return $this->belongsTo(Lga::class, 'lga_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->surname,
        ])));
    }

    public function getInitialsAttribute(): string
    {
        $first = mb_substr((string) $this->first_name, 0, 1);
        $last = mb_substr((string) $this->surname, 0, 1);

        return strtoupper($first.$last);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public const FIELD_LABELS = [
        'client_id' => 'Client ID',
        'surname' => 'Surname',
        'first_name' => 'First Name',
        'middle_name' => 'Middle Name',
        'sex' => 'Sex',
        'date_of_birth' => 'Date of Birth',
        'mobile_number' => 'Mobile Number',
        'email' => 'Email',
        'amount_to_invest' => 'Amount to Invest',
        'bank_name' => 'Bank Name',
        'account_name' => 'Account Name',
        'account_number' => 'Account Number',
        'bvn' => 'BVN',
        'occupation' => 'Occupation',
        'employer_name' => 'Employer Name',
        'state' => 'State of Origin',
        'lga' => 'LGA',
    ];

    public function reportField(string $field): mixed
    {
        return match ($field) {
            'bank_name' => $this->bank?->name ?? '—',
            'state' => $this->stateOfOrigin?->name ?? '—',
            'lga' => $this->lga?->name ?? '—',
            'date_of_birth' => format_date($this->date_of_birth),
            'amount_to_invest' => format_money($this->amount_to_invest),
            'bvn' => $this->bvn ?? '—',
            default => $this->{$field} ?? '—',
        };
    }

    public function getHasPortfolioAttribute(): bool
    {
        return $this->portfolio()->exists();
    }

    public static function nextClientId(): string
    {
        $last = static::withTrashed()->orderByDesc('id')->value('client_id');
        $number = 1;

        if ($last && preg_match('/^YFC-(\d+)$/', $last, $m)) {
            $number = (int) $m[1] + 1;
        }

        return sprintf('YFC-%04d', $number);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, function (Builder $q, string $search) {
            $q->where(function (Builder $inner) use ($search) {
                $inner->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('surname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%")
                    ->orWhere('client_id', 'like', "%{$search}%");
            });
        });
    }
}