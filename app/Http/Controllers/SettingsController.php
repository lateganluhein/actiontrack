<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Show the settings form.
     */
    public function edit(): View
    {
        $settings = auth()->user()->getOrCreateSettings();

        return view('settings.edit', compact('settings'));
    }

    /**
     * Update the user's settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'daily_summary_enabled' => 'boolean',
            'daily_summary_time' => 'required|date_format:H:i',
            'email_notifications' => 'boolean',
        ]);

        $settings = auth()->user()->getOrCreateSettings();

        $settings->update([
            'daily_summary_enabled' => $request->boolean('daily_summary_enabled'),
            'daily_summary_time' => $validated['daily_summary_time'],
            'email_notifications' => $request->boolean('email_notifications'),
        ]);

        return redirect()
            ->route('settings.edit')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($validated);

        return redirect()
            ->route('settings.edit')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('settings.edit')
            ->with('success', 'Password updated successfully.');
    }
}
