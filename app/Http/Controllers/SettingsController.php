<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailSettingsRequest;
use App\Http\Requests\SettingsRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    private const BRAND_ASSETS = ['company_logo', 'company_favicon', 'report_logo'];

    public function index()
    {
        $this->authorize('manage settings');

        return view('settings.index');
    }

    public function update(SettingsRequest $request)
    {
        $data = $request->validated();

        foreach (self::BRAND_ASSETS as $field) {
            if ($request->hasFile($field)) {
                if (($old = Setting::get($field)) && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }

                $data[$field] = $request->file($field)->store('branding', 'public');
            } else {
                unset($data[$field]);
            }
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        activity()
            ->causedBy($request->user())
            ->withProperties(array_keys($data))
            ->log('updated settings');

        return back()->with('toast', ['type' => 'success', 'message' => 'Settings saved successfully.']);
    }

    public function emailIndex()
    {
        $this->authorize('manage settings');

        return view('settings.email');
    }

    public function emailUpdate(EmailSettingsRequest $request)
    {
        $data = $request->validated();

        if (empty($data['mail_password'])) {
            unset($data['mail_password']);
        }

        foreach ($data as $key => $value) {
            Setting::set($key, (string) $value);
        }

        activity()
            ->causedBy($request->user())
            ->withProperties(array_keys($data))
            ->log('updated email settings');

        return back()->with('toast', ['type' => 'success', 'message' => 'Email settings saved successfully.']);
    }
}
