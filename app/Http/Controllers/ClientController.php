<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Bank;
use App\Models\Client;
use App\Models\Document;
use App\Notifications\ClientRegisteredNotification;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Client::class);

        return view('clients.index');
    }

    public function create()
    {
        $this->authorize('create', Client::class);

        return view('clients.create', [
            'client' => new Client,
            'states' => State::with('lgas')->orderBy('name')->get(),
            'banks' => Bank::orderBy('name')->get(),
        ]);
    }

    public function store(ClientRequest $request)
    {
        $data = $request->validated();
        $data['client_id'] = Client::nextClientId();
        $data['created_by'] = $request->user()->id;

        $client = Client::create($data);

        if (($kin = $request->input('next_of_kin')) && ($kin['name'] ?? null)) {
            $client->nextOfKin()->create($kin);
        }

        $this->storeDocuments($request, $client);

        Notification::send(User::all(), new ClientRegisteredNotification($client));

        return redirect()->route('clients.edit', $client)
            ->with('toast', ['type' => 'success', 'message' => "Client {$client->client_id} registered successfully."]);
    }

    public function edit(Client $client)
    {
        $this->authorize('update', $client);

        $client->load(['nextOfKin', 'documents', 'stateOfOrigin', 'lga', 'bank']);

        return view('clients.edit', [
            'client' => $client,
            'states' => State::with('lgas')->orderBy('name')->get(),
            'banks' => Bank::orderBy('name')->get(),
        ]);
    }

    public function update(ClientRequest $request, Client $client)
    {
        $data = $request->validated();

        $client->update($data);

        if (($kin = $request->input('next_of_kin')) && ($kin['name'] ?? null)) {
            $client->nextOfKin()->updateOrCreate([], $kin);
        } elseif ($client->nextOfKin()->exists()) {
            $client->nextOfKin()->delete();
        }

        $this->storeDocuments($request, $client);

        return redirect()->route('clients.edit', $client)
            ->with('toast', ['type' => 'success', 'message' => 'Client information updated successfully.']);
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);

        $client->delete();

        return redirect()->route('clients.index')
            ->with('toast', ['type' => 'success', 'message' => "Client {$client->client_id} deleted."]);
    }

    public function approve(Client $client)
    {
        $this->authorize('update', $client);

        $client->update(['status' => Client::STATUS_APPROVED]);

        activity()
            ->performedOn($client)
            ->causedBy(request()->user())
            ->log('approved client registration');

        return redirect()->route('clients.index')
            ->with('toast', ['type' => 'success', 'message' => "Client {$client->client_id} approved."]);
    }

    public function downloadDocument(Client $client, Document $document)
    {
        abort_unless($document->client_id === $client->id, 404);

        $this->authorize('view', $client);

        return Storage::disk('public')->download($document->stored_path, $document->original_name);
    }

    public function destroyDocument(Client $client, Document $document)
    {
        abort_unless($document->client_id === $client->id, 404);

        $this->authorize('update', $client);

        Storage::disk('public')->delete($document->stored_path);
        $document->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Document removed.']);
    }

    protected function storeDocuments(Request $request, Client $client): void
    {
        $maxSizeMb = (int) setting('upload_max_size', 5);

        $files = collect($request->allFiles())->filter(fn ($file, $key) => str_starts_with($key, 'documents.'));

        foreach ($files as $key => $file) {
            $type = explode('.', $key)[1];
            $validated = $request->validate([
                $key => ['file', 'mimes:pdf,jpg,jpeg,png', "max:{$maxSizeMb}"],
            ]);

            $original = $file->getClientOriginalName();
            $path = $file->store('documents/'.$client->id, 'public');

            $client->documents()->updateOrCreate(['type' => $type], [
                'original_name' => $original,
                'stored_path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }
}