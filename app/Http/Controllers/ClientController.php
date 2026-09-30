<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Models\Document;
use App\Models\State;
use App\Models\User;
use App\Notifications\ClientRegisteredNotification;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Client::class);

        return view('clients.index');
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);

        $client->load(['nextOfKin', 'documents', 'stateOfOrigin', 'lga', 'bank', 'portfolio', 'creator']);

        return view('clients.show', [
            'client' => $client,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Client::class);

        return view('clients.create', [
            'client' => new Client,
            'states' => State::with('lgas')->orderBy('name')->get(),
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

    public function regeneratePortfolioAccess(Client $client)
    {
        $this->authorize('update', $client);

        if (! $client->hasPortfolioAccess()) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Set an access code before generating the portfolio link.',
            ]);
        }

        $client->regeneratePortfolioAccessToken();

        activity()
            ->performedOn($client)
            ->causedBy(request()->user())
            ->log('regenerated portfolio access link');

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'A new portfolio link has been generated. The previous link no longer works.',
        ]);
    }

    public function emailPortfolioAccess(Client $client, EmailService $email)
    {
        $this->authorize('update', $client);

        if (! $client->hasPortfolioAccess()) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Set an access code before emailing the portfolio link.',
            ]);
        }

        if (! $client->email) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'This client has no email address on file.',
            ]);
        }

        try {
            $sent = $email->sendPortfolioAccessLink($client);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Failed to send the email: '.$e->getMessage(),
            ]);
        }

        if (! $sent) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Email settings are not configured. Set them up under Email Settings first.',
            ]);
        }

        activity()
            ->performedOn($client)
            ->causedBy(request()->user())
            ->log('emailed portfolio access link to '.$client->email);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Portfolio link emailed to '.$client->email.'.',
        ]);
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
        $maxSizeKb = $maxSizeMb * 1024;

        $files = collect($request->file('documents', []))->filter(
            fn ($file) => $file instanceof UploadedFile
        );

        foreach ($files as $type => $file) {
            $request->validate([
                "documents.{$type}" => ['file', 'mimes:pdf,jpg,jpeg,png', "max:{$maxSizeKb}"],
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
