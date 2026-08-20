<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Attributes\On;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';

    public bool $open = false;

    public function updatedQuery(): void
    {
        $this->open = strlen(trim($this->query)) >= 2;
    }

    public function results()
    {
        $q = trim($this->query);

        if (strlen($q) < 2) {
            return collect();
        }

        return Client::query()
            ->with('portfolio')
            ->search($q)
            ->orderBy('surname')
            ->limit(8)
            ->get();
    }

    #[On('search:reset')]
    public function resetSearch(): void
    {
        $this->query = '';
        $this->open = false;
    }

    public function selectClient(Client $client): void
    {
        $this->dispatch('search:reset');
        $this->redirect(route('clients.edit', $client));
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}