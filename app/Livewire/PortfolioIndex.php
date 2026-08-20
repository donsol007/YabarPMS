<?php

namespace App\Livewire;

use App\Models\Portfolio;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PortfolioIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'sort', history: true)]
    public string $sortField = 'created_at';

    #[Url(as: 'dir', history: true)]
    public string $sortDirection = 'desc';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete(Portfolio $portfolio): void
    {
        $this->authorize('delete', $portfolio);

        $portfolio->delete();

        $this->dispatch('toast', type: 'success', message: 'Portfolio deleted.');
    }

    #[Computed]
    public function portfolios()
    {
        $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return Portfolio::query()
            ->with(['client'])
            ->when($this->search, function ($q) {
                $q->whereHas('client', function ($client) {
                    $client->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('middle_name', 'like', "%{$this->search}%")
                        ->orWhere('surname', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('client_id', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortField, $direction)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.portfolio-index');
    }
}