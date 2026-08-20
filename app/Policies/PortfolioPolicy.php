<?php

namespace App\Policies;

use App\Models\Portfolio;
use App\Models\User;

class PortfolioPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view portfolios');
    }

    public function view(User $user, Portfolio $portfolio): bool
    {
        return $user->can('view portfolios');
    }

    public function create(User $user): bool
    {
        return $user->can('create portfolios');
    }

    public function update(User $user, Portfolio $portfolio): bool
    {
        return $user->can('edit portfolios');
    }

    public function delete(User $user, Portfolio $portfolio): bool
    {
        return $user->can('delete portfolios');
    }

    public function generate(User $user): bool
    {
        return $user->can('generate reports');
    }
}