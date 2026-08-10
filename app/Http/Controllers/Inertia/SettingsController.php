<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Settings\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(Request $request): Response
    {
        $company = $request->user()->company;

        $settings = Setting::whereIn('group', ['general', 'invoice', 'order', 'inventory'])
            ->get()
            ->groupBy('group')
            ->map(fn ($items) => $items->pluck('value', 'key'));

        return Inertia::render('Settings/Index', [
            'company' => $company,
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully');
    }

    public function company(Request $request): Response
    {
        $company = $request->user()->company()->load('settings');

        return Inertia::render('Settings/Company', [
            'company' => $company,
        ]);
    }

    public function updateCompany(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('company', 'public');
        }

        $request->user()->company()->update($validated);

        return redirect()->route('settings.company')->with('success', 'Company settings updated successfully');
    }
}
