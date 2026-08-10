<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $settings = Setting::where('group', $request->get('group', 'general'))->get();

        return $this->success($settings, 'Settings retrieved successfully');
    }

    public function show(string $key): JsonResponse
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return $this->notFound('Setting not found');
        }

        return $this->success($setting);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|max:100',
            'settings.*.value' => 'required|string',
        ]);

        foreach ($validated['settings'] as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                ['value' => $item['value']]
            );
        }

        return $this->success(message: 'Settings updated successfully');
    }

    public function getGroup(string $group): JsonResponse
    {
        $settings = Setting::where('group', $group)->get();

        $formatted = $settings->pluck('value', 'key');

        return $this->success($formatted, 'Settings retrieved successfully');
    }

    public function updateGroup(Request $request, string $group): JsonResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => $group],
                ['value' => $value]
            );
        }

        return $this->success(message: "Settings for group '{$group}' updated successfully");
    }
}
