<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\ClientRegistrationInvite;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ClientIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'sort', history: true)]
    public string $sortField = 'created_at';

    #[Url(as: 'dir', history: true)]
    public string $sortDirection = 'desc';

    public ?string $shareLink = null;

    public function generateShareLink(): void
    {
        $this->authorize('create', Client::class);

        $invite = ClientRegistrationInvite::issue(auth()->id());

        $this->shareLink = route('client.register', ['token' => $invite->token]);

        $this->dispatch('open-modal', 'share-registration-link');
    }

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

    public function delete(Client $client): void
    {
        $this->authorize('delete', $client);

        $client->delete();

        $this->dispatch('toast', type: 'success', message: "Client {$client->client_id} deleted.");
    }

    public function approve(Client $client): void
    {
        $this->authorize('update', $client);

        $client->update(['status' => Client::STATUS_APPROVED]);

        activity()
            ->performedOn($client)
            ->causedBy(auth()->user())
            ->log('approved client registration');

        $this->dispatch('toast', type: 'success', message: "Client {$client->client_id} approved.");
    }

    #[Computed]
    public function clients()
    {
        $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return Client::query()
            ->with(['portfolio'])
            ->search($this->search)
            ->orderBy($this->sortField, $direction)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.client-index');
    }
}
