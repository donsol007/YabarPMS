<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffStoreRequest;
use App\Http\Requests\StaffUpdateRequest;
use App\Models\User;
use App\Notifications\StaffCreatedNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $this->authorize('manage staff');

        $staff = User::with('roles')->orderBy('name')->paginate(15);

        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        $this->authorize('manage staff');

        return view('staff.create');
    }

    public function store(StaffStoreRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($data['role'] ?? 'staff');

        activity()
            ->performedOn($user)
            ->causedBy($request->user())
            ->withProperties(['email' => $user->email])
            ->log('created staff account');

        Notification::send(User::role('admin')->get(), new StaffCreatedNotification($user));

        return redirect()->route('staff.index')
            ->with('toast', ['type' => 'success', 'message' => "Staff account for {$user->name} created. They can now log in."]);
    }

    public function edit(User $user)
    {
        $this->authorize('manage staff');

        return view('staff.edit', compact('user'));
    }

    public function update(StaffUpdateRequest $request, User $user)
    {
        $data = $request->validated();

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        if (filled($data['password'] ?? null)) {
            $user->update(['password' => $data['password']]);
        }

        if (isset($data['role']) && $data['role'] !== '') {
            $user->syncRoles([$data['role']]);
        }

        activity()
            ->performedOn($user)
            ->causedBy($request->user())
            ->withProperties(['email' => $user->email])
            ->log('updated staff account');

        return redirect()->route('staff.index')
            ->with('toast', ['type' => 'success', 'message' => "Staff account for {$user->name} updated."]);
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorize('manage staff');

        if ($user->id === $request->user()->id) {
            return back()->with('toast', ['type' => 'error', 'message' => 'You cannot delete your own account.']);
        }

        $name = $user->name;
        $user->delete();

        activity()
            ->causedBy($request->user())
            ->withProperties(['deleted_user' => $name])
            ->log('deleted staff account');

        return redirect()->route('staff.index')
            ->with('toast', ['type' => 'success', 'message' => "Staff account for {$name} deleted."]);
    }
}