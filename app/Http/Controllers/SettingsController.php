<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
}
