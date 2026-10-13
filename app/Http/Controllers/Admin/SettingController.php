<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::query()->orderBy('group')->orderBy('key')->get()->keyBy('key');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:80'],
            'site_tagline' => ['nullable', 'string', 'max:160'],
            'contact_email' => ['required', 'email', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:200'],
            'platform_fee_percent' => ['required', 'integer', 'between:0,100'],
            'refund_policy' => ['nullable', 'string', 'max:1000'],
        ]);

        foreach ($validated as $key => $value) {
            $group = match ($key) {
                'site_name', 'site_tagline' => 'general',
                'contact_email', 'contact_phone', 'address' => 'contact',
                default => 'finance',
            };

            Setting::set($key, $value, $group);
        }

        return back()->with('success', 'Pengaturan disimpan.');
    }
}
