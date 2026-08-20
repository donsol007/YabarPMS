<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code'])]
class Bank extends Model
{
    use HasFactory;

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'bank_id');
    }
}